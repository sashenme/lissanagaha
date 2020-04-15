<?php

namespace App\Http\Controllers;

use App\Answer;
use App\Filters\Common;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AnswerController extends Controller
{
    static $config = [
        'randFirst' => 0,
        'randLast' => 3,
        'disallowingMinutes' => 5,
        'maxLimit' => 2000
    ];


    public function create()
    {

        $lg = $this;
        $randomNumber = self::getLuckyNumber();
        $question = Common::$randomQuistion[$randomNumber];
        return view('solve',compact('lg'))->with('question', $question)->with('q', $randomNumber);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'answer' => 'required'
        ]);

        if (self::isEligible()){
            if ($request->get('answer') == Common::$randomAnswers[$request->get('q')]) {

                $answers = Answer::updateOrcreate(['id' => $request->get('id')], [
                    'user_id' => 1,
                    'value' => 10,
                ]);
    
                return redirect()->back();
            } else {
                return 'waradi bro';
            }
        }else{
            return 'tho elible naa';
        }
           

   
    }

    function getLuckyNumber()
    {
        $luckyNumber = rand(self::$config['randFirst'], self::$config['randLast']);

        return $luckyNumber;
    }

    static function myResult()
    {
        return Answer::where('user_id', 1)->sum('value');
    }

    static function getLast()
    {
        return Answer::where('user_id', 1)->orderBy('id', 'desc')->first();
    }

    static function isExisting()
    {
        return !empty(self::getLast());
    }

    static function isEligible()
    {
        $last = self::getLast();

        if (empty($last))
            return true;

        return $last->created_at->addMinutes(self::$config['disallowingMinutes'])->lt(Carbon::now());
    }
}