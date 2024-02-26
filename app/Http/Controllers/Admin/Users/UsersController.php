<?php

use App\Http\Controllers\Admin\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\USers\UsersCollection;
use Illuminate\Support\Facades\Storage;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->get("search");
        $state = $request->get("state");

        $users = User:: orderBy("id","desc")->where ("type_user")->get();
        return response()->json([
            "message"=> 200,
            "users" =>UsersCollection::make($users),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user_v = User::where ("email",$request->email)->first();
        if ($user_v) {
            return response()->json([
                "message" => 403,
                "message_text" => "EL USUARIO YA EXISTE"
            ]);
        }

        if ($request->hasfile("img")){
            $path = Storage::putFile("users",$request->file("img"));
            $request->request->add(["avatar"=>$path]);
        }

        if ($request->new_password){
            $request->request->add(["password" => bcryp($request->new_password)]);
        }
        $user = User::create($request->all());

        return response()-> json([
            "message" => 200,
            "user" => UsersResource::make($user),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user_v = User::where("id","<>",$id) -> where ("email",$request->email)->first();
        if ($user_v) {
            return response()->json([
                "message" => 403,
                "message_text" => "EL USUARIO YA EXISTE"
            ]);
        }
        $user = User::findORFail($id);

        if ($request->hasfile("img")){
            if($user->avatar){
                Storage::delete($user->avatar);
            }
            $path = Storage::putFile("users",$request->file("img"));
            $request->request->add(["avatar"=>$path]);
        }

        if ($request->new_password){
            $request->request->add(["password" => bcryp($request->new_password)]);
        }
        $user = User::create($request->all());

        $user->update($request->all());

        return response()-> json([
            "message" => 200,
            "user" => UsersResource::make($user),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findORFail($id);
        $user->delete();
        return response()->json (["message"=>200]);
    }
}
