<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\ContactMessage;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

use App\Models\Subscriber;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Mail;

class SubscriberController extends Controller
{
    use ResponseTrait;

    /***************** DW ************* */
    public function getUserById($id)
    {
        try {

            $sub = Subscriber::findOrFail($id);


            return response()->json([
                'success' => true,
                'message' => 'User data retrieved successfully.',
                'data' => $sub,
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function getUserByEmail($email)
    {
        try {

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid email format.',
                ], 400);
            }


            $sub = Subscriber::where('email', $email)->first();


            if (!$sub) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found.',
                ], 404);
            }


            return response()->json([
                'success' => true,
                'message' => 'User data retrieved successfully.',
                'data' => $sub,
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }



    public function send_email(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:50',
            'code' => 'required|string'
        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }



        try {
            $code = $request->code;

            if (send_Email($request->email, $code)) {
                return $this->returnSuccess("we sent your verification code  succesfully ");
            }

            return $this->returnSuccess('you are verified your account successfully :)');
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }


    /*************** */
    public function signUp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:50|unique:subscribers',
            'password' => 'required|string|min:8',
            'full_name' => 'required|string',
            'username' => 'required|min:4|max:20',
            'mobile' => 'required|regex:/^\+?\d{10,15}$/',
            'country_code_id' => 'required|exists:country_codes,id'
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        try {

            $subscriber = Subscriber::create([
                'full_name' => $request->full_name,
                'password' => Hash::make($request->password),
                'email' => $request->email,
                'username' => $request->username,
                'verification_code' => null,
                'country_code_id' => $request->country_code_id,
                'mobile' => $request->mobile,
                'is_active' => false
            ]);


            Cart::create([
                'sub_id' => $subscriber->id,
            ]);


            try {
                $code = sendEmail($request->email);
                if ($code) {

                    $subscriber->verification_code = $code;
                    $subscriber->save();
                    return $this->returnSuccess('Your account has been created successfully. Please check your email for verification.', 200);
                }
                return $this->returnError('there are an error in sending email');
            } catch (\Exception $e) {
            }
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }


    public function check()
    {
        if (Auth::guard("sub")->user()) {
            return $this->returnSuccess("You are logged in  :)");
        } else {
            return $this->returnError("You are not logged in yet :(");
        }
    }
    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6',
            'email' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        try {
            $cre = $request->only('password', 'email');

            $token = Auth::guard('sub')->attempt($cre);

            if ($token) {

                $subscriber = Auth::guard('sub')->user();

                $subscriber->token = $token;
                $subscriber->country_code_number = $subscriber->countryCode->country_code;

                return $this->returnData('', $subscriber, 200,);
            }
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
        return $this->returnError('your data is invalid');
    }

    public function resend(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:50',
        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        try {
            if (sendEmail($request->email)) {
                return $this->returnSuccess("we sent your verification code  succesfully ");
            }
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
        return $this->returnError(msgErorr: "this email does not exist");
    }

    public function logout()
    {

        Auth::guard('sub')->logout();
        return $this->returnSuccess('your are logged-out successfully');
    }

    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'nationality' => "nullable|string",
            'birthdate' => "nullable|date",
            'gender' => "nullable|numeric|max:1|in:0,1",
            'address' => "nullable|string",
            "mobile" => "string | max:10 | min:10"
        ]);



        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }
        try {

            $sub = Subscriber::findOrFail($id);
            $isUpdated = $sub->update($request->all());

            if ($isUpdated) {

                return $this->returnSuccess('your data is updates successfully');
            }
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
        return $this->returnError('error');
    }

    public function verify(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'verification_code' => 'required | string | min:6|max:6'
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        try {
            $sub = Auth::guard('sub')->user();
            if ($sub && $sub->verification_code == $request->verification_code) {
                $sub->is_active = true;
                $sub->is_verified = true;
                $sub->save();
                return $this->returnSuccess('you are verified your account successfully :)');
            }
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
        return $this->returnError("your code is not correct ");
    }

    public function changePassword(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [

            'old_password' => 'required|string|min:6',
            'new_password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        try {
            $email = $request->email;
            $sub = Subscriber::findOrFail($id);
            if ($sub->email != $email) {
                return $this->returnError('uncorrect email :(');
            } else if (!Hash::check($request->old_password, $sub->password)) {
                return $this->returnError('uncorrect password :(');
            }
            $sub->password = Hash::make($request->new_password);
            $sub->save();
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }

        return $this->returnSuccess('your password is updated ');
    }

    public function rpStep1(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => "required |exists:subscribers,email",
        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        $sub = Subscriber::where('email', "=", $request->email)->firstOrFail();
        $resetCode = makeResetCode();
        $sub->update([
            "reset_token" => Hash::make($resetCode),
        ]);
        try {
            \Illuminate\Support\Facades\Mail::to($sub->email)->send(new \App\Mail\Subscriber($resetCode));
        } catch (TransportExceptionInterface $e) {
            return $this->returnError($e->getMessage());
        }
        return $this->returnSuccess('we have sent a code for you ... check your email');
    }

    public function rpStep2(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reset_token' => "required",
            'email' => "required |exists:subscribers,email",
        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        try {
            $sub = Subscriber::where('email', "=", $request->email)->firstOrFail();
            $is = Hash::check($request->reset_token, $sub->reset_token);
            if ($is) {
                return $this->returnSuccess('correct code ');
            }
            return $this->returnError('code does not match with code in your email ');
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }
    public function rpStep3(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => "required |exists:subscribers,email",
            "new_password" => "string| min:8"
        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        try {
            $sub = Subscriber::where('email', "=", $request->email)->firstOrFail();
            $sub->password = Hash::make($request->new_password);
            $sub->save();
            return $this->returnSuccess('your pass is updated successfully :)');
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

    public function contactUs(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'message' => 'required|string|max:500',
        ]);


        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first(), 400);
        }

        $message = "You have received a new contact us message:\n\n" .
            "Full Name: {$request->full_name}\n" .
            "Email: {$request->email}\n" .
            "Message:\n{$request->message}";

        //$to = "info@wemarketglobal.com";
        $success = send_contact_us( $message, $request->email, $request->full_name);

        if ($success) {
            return $this->returnSuccess("Your message has been sent successfully");
        } else {
            return $this->returnError("Failed to send your message. Please try again later");
        }
    }
}
