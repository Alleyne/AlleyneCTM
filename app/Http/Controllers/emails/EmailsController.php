<?php

namespace App\Http\Controllers\emails;

use App\User;
use App\Mail\nuevoEcuentas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;

class EmailsController extends Controller
{
    /**
     * Ship the given order.
     *
     * @param  Request  $request
     * @param  int  $orderId
     * @return Response
     */
    public function emailNuevoEcuentas()
    {
        $datos = User::findOrFail(9);
        Mail::to($datos->email)
            ->cc($datos->email)
            ->bcc($datos->email)
            ->send(new nuevoEcuentas($datos));
    }
}