<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Faker\Factory;
use Faker\Provider\Uuid;
use Illuminate\Support\Facades\Redis;

class TransactionControllerTest extends TestCase
{
    use WithoutMiddleware;

    /*
     * Test transactions PUT | GET
     * @author Mehdi Hosseini <mehdi.hosseini.dev@gmail.com
     * @since 17 Jan 2018
     */
    public function testTransaction()
    {

        $parent_id = 0;

        // Create 100 test case to be asserted.
        for($i=0;$i<=100; $i++){

           // Generated fake value for the test
          $faker = Factory::create();

          // All types of categories(types) for our transactions
          $types_input = ['cars', 'home', 'fruits', 'phone', 'laptop', 'computer', 'notebook', 'tv', 'shoe', 'gum'];

          // Set random types from previous list to every transaction
          $type = $types_input[rand(0,9)];

          $wrong_types_input = ['cars1', 'home$', 'fruits#', 'phone.', 'laptop_', 'computer@', 'notebook87', 't2v', 'shoe%', 'g#um'];

          $wrong_type = $wrong_types_input[rand(0,9)];

          // Generate unique number to transaction between from 1000 to 20000
          $transaction_id = $faker->unique()->numberBetween(1000, 20000);

          // Set random transaction between 1000 to 20000 by step 1000
          $transaction_amount = rand(0,20)*1000;

          // Divided every transaction parent to 10th transaction to be tested.
          if ( $i%10 == 0 || $parent_id == 0  ) {

            $parent_id = $transaction_id;

          }

           /*
            * Test update action as you described before
            * PUT /transactionservice/transaction/$transaction_id
            */
           $this->put('/transactionservice/transaction/test:' . $transaction_id, [
               'amount' => $transaction_amount,
               'type' => $wrong_type,
               'parent_id' => 'test:' . $parent_id
               ])
               ->assertStatus(300)
               ->assertJsonCount(2)
               ->assertJson([
                    'status' => 'validation-error'
                ]);

            /*
             * Test update action as you described before
             * PUT /transactionservice/transaction/$transaction_id
             */
            $this->put('/transactionservice/transaction/test:' . $transaction_id, [
                'amount' => $transaction_amount,
                'type' => $type,
                'parent_id' => 'test:' . $parent_id
                ])
                ->assertStatus(200)
                ->assertJsonCount(1)
                ->assertJson([
                     'status' => 'ok',
                 ]);

          /*
           * Test put action according to document.
           */
          $this->get('/transactionservice/transaction/test:' .  $transaction_id )
               ->assertStatus(200)
               ->assertJsonCount(3)
               ->assertJson([
                   'amount' => $transaction_amount,
                   'type' => $type,
                   'parent_id' => 'test:' . $parent_id
               ]);

        }

        /*
         * Test update action as you described before
         * PUT /transactionservice/transaction/$transaction_id
         */
        $this->put('/transactionservice/transaction/test:10', [])
            ->assertStatus(300)
            ->assertJsonCount(2)
            ->assertJson([
                 'status' => 'validation-error',
                 'error' => [
                      "The amount field is required.",
                      "The type field is required."
                  ]
             ]);

         /*
          * Test update action with one empty input fields
          */
         $this->put('/transactionservice/transaction/test:10', [
           'amount' => $faker->numberBetween(1000, 20000)
         ])
             ->assertStatus(300)
             ->assertJsonCount(2)
             ->assertJson([
                  'status' => 'validation-error',
                  'error' => [
                       "The type field is required."
                   ]
              ]);

          /*
           * Test update action with one empty input fields
           */
          $this->put('/transactionservice/transaction/test:10', [
            'type' => 'cars'
          ])
              ->assertStatus(300)
              ->assertJsonCount(2)
              ->assertJson([
                   'status' => 'validation-error',
                   'error' => [
                        "The amount field is required.",
                    ]
               ]);

    }

    /*
     * Test transactions's sum
     * @author Mehdi Hosseini <mehdi.hosseini.dev@gmail.com
     * @since 17 Jan 2018
     */
    public function testSum(){

      $sum = 0;

      // Fetch all transactions list from redis to test
      $all_transactions = Redis::command('keys', ['transaction:test:*']);

      $transaction = Redis::command('keys', ['transaction:test:*']);

      // Calculate the sum of transactions
      foreach( $all_transactions as $transaction_id ){

        foreach( $transaction as $value ){

          $keys = json_decode(Redis::get($value));

          if ( $keys->parent_id == $transaction_id ) {

            $sum += $keys->amount;

          }

        }

        /*
         * Request to API to calculate the sum
         * GET /transactionservice/sum/$transaction_id
         */
        $this->get('transactionservice/sum/test:' . $transaction_id )
             ->assertStatus(200)
             ->assertJsonCount(1)
             ->assertJson([
                  'sum' => $sum
              ]);

      }

    }

    /*
     * Test transactions's type
     * @author Mehdi Hosseini <mehdi.hosseini.dev@gmail.com
     * @since 17 Jan 2018
     */
    public function testType(){

      $type = 0;

      // Fetch all transactions list from redis to test
      $all_transactions = Redis::command('keys', ['transaction:test:*']);

      $transaction = Redis::command('keys', ['transaction:test:*']);

      // Calculate the type of transactions
      foreach( $all_transactions as $transaction_id ){

        foreach( $transaction as $value ){

          $keys = json_decode(Redis::get($value));

          if ( $keys->type == $transaction_id ) {

            $type++;

          }

        }

        /*
         * Request to API to calculate the type
         * GET /transactionservice/type/$transaction_id
         */
        $this->get('transactionservice/types/test:' . $transaction_id )
             ->assertStatus(200)
             ->assertJsonCount(1)
             ->assertJson([
                  'types' => $type
              ]);

      }

    }

    /*
     * Test delete all transactions:test:* keys
     * @author Mehdi Hosseini <mehdi.hosseini.dev@gmail.com
     * @since 17 Jan 2018
     */
    public function testDeleteTransactions(){

      // Fetch all  transactions:test:* keys from redis
      $transaction = Redis::command('keys', ['transaction:test:*']);

      foreach( $transaction as $value ){

        $value = str_replace('transaction:test:', '', $value);

        /*
         * Send request to API to delete the key
         */
        $this->delete('/transactionservice/transaction/test:' . $value )
               ->assertStatus(200)
               ->assertJsonCount(1)
               ->assertJson([
                   'status' => 'ok'
               ]);

      }

    }

}
