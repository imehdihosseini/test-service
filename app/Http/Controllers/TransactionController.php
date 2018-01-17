<?php

namespace App\Http\Controllers;

use Illuminate\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Transaction;
use Illuminate\Support\Facades\Redis;
use Validator;

class TransactionController extends Controller
{

  /*
   * Get current transaction with transaction's id
   * @author Mehdi Hosseini <mehdi.hosseini.dev@gmail.com
   * @param integer $id
   * @since 17 Jan 2018
   * @return JSON
   */
    public function show($id)
    {
        try {

          /*
           * Fetch current transaction from Redis
           * The requested field will be decoded to JSON format
           */
          $transaction = json_decode(
            Redis::get('transaction:'.$id)
          );

        } catch(Exception $e){

          throw new HttpException(400, "Something went wrong!");

        }

        return response()->json(
          $transaction, 200
        );

    }

    /*
     * Remove current transaction with transaction' id
     * @author Mehdi Hosseini <mehdi.hosseini.dev@gmail.com>
     * @param integer $id
     * @since 17 Jan 2018
     * @return JSON
     */
    public function destroy($id)
    {

        try {

          // Remove current transactin id from Redis
          Redis::del('transaction:' . $id);

        } catch(Exception $e){

          throw new HttpException(400, "Something went wrong!");

        }

        return response()->json(
            [ "status" => "ok"], 200
        );

    }

    /*
     * Return count of transactions with specifict type
     * @author Mehdi Hosseini <mehdi.hosseini.dev@gmail.com>
     * @param string $type
     * @since 17 Jan 2018
     * @return JSON
     */
    public function types($type){

      $types = 0;

      try {

        // Select all keys which starts with transactions keyword
        $transaction = Redis::command('keys', ['transaction:*']);

        foreach( $transaction as $value ){

          // Fetch every element from redis one by one
          $keys = json_decode(Redis::get($value));

          if ( $keys->type == $type ) {

            $types++;

          }

        }

      } catch(Exception $e) {

        throw new HttpException(400, "Something went wrong!");

      }

      return response()->json(
          [ "types" => $types], 200
      );

    }

    /*
     * Update current transaction or add new one
     * @author Mehdi Hosseini <mehdi.hosseini.dev@gmail.com>
     * @param object $request
     * @param integer $id
     * @since 17 Jan 2018
     * @return JSON
     */
    public function update(Request $request, $id)
    {
      try {

        $validator = Validator::make($request->all(), [
            'amount' => 'required|integer',
            'type' => 'required|alpha'
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                  "status" => "validation-error",
                  "error" => $validator->errors()->all()
                ], 300
            );
        }

         // Update and set new keys on Redis then encode them
         Redis::set('transaction:'.$id, json_encode([
                                                      "amount" => $request->input('amount'),
                                                      "type" => $request->input('type'),
                                                      "parent_id" => $request->input('parent_id')
                                                    ]));

      } catch(Exception $e) {

        throw new HttpException(400, "Something went wrong!");

      }

      return response()->json(
          [ "status" => "ok"], 200
      );

    }

    /*
     * Return sum of related transactions to current transaction id
     * @author Mehdi Hosseini <mehdi.hosseini.dev@gmail.com>
     * @param integer $id
     * @since 17 Jan 2018
     * @return JSON
     */
    public function sum($id){

      $sum = 0;

      try {

        // Select all keys which starts with transactions keyword
        $transaction = Redis::command('keys', ['transaction:*']);

        foreach( $transaction as $value ){

          // Fetch every element from redis one by one
          $keys = json_decode(Redis::get($value));

          if ( $keys->parent_id == $id ) {

            $sum += $keys->amount;

          }

        }

      } catch(Exception $e) {

        throw new HttpException(400, "Something went wrong!");

      }

      return response()->json(
          [ "sum" => $sum], 200
      );
    }
}
