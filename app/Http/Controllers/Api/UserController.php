<?php

use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class UserController extends Controller
{
	// ユーザー一覧
	public function index()
	{
		return UserResource::collection(User::all());
	}

	// ユーザー作成
	public function store(Request $request)
	{
		$user = User::create($request->validate([
			'name' => 'required',
			'email' => 'required|email|unique:users',
			'password' => 'required|min:8',
		]));
		return new UserResource($user);
	}

	// 特定ユーザー取得
	public function show(User $user)
	{
		return new UserResource($user);
	}
	public function update(Request $request, User $user)
	{
		// 1. バリデーション
		// PATCHの場合は一部の項目のみが送られてくるため 'sometimes' を活用するのがコツです
		$validatedData = $request->validate([
			'name' => 'sometimes|required|string|max:255',
			'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
			'password' => 'sometimes|required|min:8',
		]);

		// 2. データの更新
		// fill() は $fillable に設定されたカラムのみを安全にセットします
		$user->fill($validatedData);

		// パスワードが含まれている場合はハッシュ化
		if ($request->has('password')) {
			$user->password = bcrypt($request->password);
		}

		$user->save();

		// 3. 更新後のデータをリソースとして返却
		return new UserResource($user);
	}
	public function destroy(User $user)
	{
		// 削除実行
		$user->delete();

		// 204 No Content (成功したが返すデータはない) 
		// または 200 OK でメッセージを返す
		return response()->json(['message' => 'User deleted successfully'], 200);
	}
}
