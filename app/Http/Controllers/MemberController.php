<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Logs;
use Illuminate\Support\Facades\Auth;

class MemberController extends Controller
{
    public function index()
    {
        $members = Member::where('status', 1)->get();
        return view('member.member', compact('members'));
    }

    public function destroy($id)
    {
        $member = Member::findOrFail($id);
        $oldData = $member->toArray();
        $member->update(['status' => 0]);

        $this->logAction(Auth::id(), 'delete', 'Member', 'Member ' . $member->nama . ' telah dinonaktifkan.', $member->id, $oldData, $member->toArray());

        return redirect('/member')->with('msg', 'Member berhasil dihapus.');
    }

    public function create()
    {
        return view('member.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|numeric',
        ]);


        $member = Member::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => 1,
        ]);

        $this->logAction(Auth::id(), 'create', 'Member', 'Member ' . $member->nama . ' telah ditambahkan.', $member->id, null, $member->toArray());

        return redirect('/member')->with('msg', 'Member berhasil ditambahkan!');
    }

    private function logAction($userId, $action, $model, $msg, $recordId = null, $oldData = null, $newData = null)
    {
        Logs::create([
            'user_id' => $userId,
            'action' => $action,
            'table_name' => $model,
            'record_id' => $recordId,
            'old_data' => $oldData ? json_encode($oldData) : null,
            'new_data' => $newData ? json_encode($newData) : null,
            'msg' => $msg,
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
            'created_at' => now(),
        ]);
    }
}
