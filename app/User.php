<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    public function microposts()
    {
        return $this->hasMany(Micropost::class);
    }
    
    public function followings()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }
    
    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
    public function favorites()
    {
        return $this->belongsToMany(Micropost::class, 'user_favorite', 'user_id', 'micropost_id')->withTimestamps();
    }
    
    public function follow($userId)
    {
        // 既にフォローしているかの確認
        $exist = $this->is_following($userId);
        // 自分自身ではないかの確認
        $its_me = $this->id == $userId;
        
        if ($exist || $its_me) {
            //既にフォローしていれば何もない
            return false;
        } else {
            //　未フォローであればフォローする
            $this->followings()->attach($userId);
            return true;
        }
    }
    
    public function unfollow($userId)
    {
        // 既にフォローしているかの確認
        $exist = $this->is_following($userId);
        // 自分自身ではないかの確認
        $its_me = $this->id == $userId;
        
        if ($exist && !$its_me) {
            // 既にフォローしていればフォローを外す
            $this->followings()->detach($userId);
            return true;
        } else {
            // 未フォローであれば何もしない
            return false;
        }
    }
    
    public function is_following($userId) {
        return $this->followings()->where('follow_id', $userId)->exists();
    }
    
    public function feed_microposts()
    {
        $follow_user_ids = $this->followings()-> pluck('users.id')->toArray();
        $follow_user_ids[] = $this->id;
        return Micropost::whereIn('user_id', $follow_user_ids);
    }
    
    // ふぁぼ関連
    
    

    public function favorite($MicropostId)
    {
        // 既にファボしているかの確認
        $exist = $this->is_favoriting($MicropostId);
        
        if ($exist) {
            // 既にファボしていれば何もしない
            return false;
        }else{
            // 未ファボであればファボする
            $this->favorites()->attach($MicropostId);
            return true;
        }
    }
    
    public function unfavorite($MicropostId)
    {
        // 既にファボしているかの確認
        $exist = $this->is_favoriting($MicropostId);
        
        if ($exist) {
            // 既にファボしていればファボを外す
            $this->favorites()->detach($MicropostId);
            return true;
        }else{
            // 未ファボであれば何もしない
            return false;
        }
    }
    
    public function is_favoriting($MicropostId)
    {
        return $this->favorites()->where('micropost_id', $MicropostId)->exists();
    }
}
