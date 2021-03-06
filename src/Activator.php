<?php

namespace Rorikurn\Activator;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Contracts\Mail\Mailer as Mail;
use Illuminate\Contracts\View\Factory as View;
use Rorikurn\Activator\UserActivation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

class Activator
{
    /**
     * Encrypter Instance
     * @var $encrypter
     */
    private $encrypter;

    /**
     * Config instance
     * @var $config
     */
    private $config;

    /**
     * View Instance
     * @var $view
     */
    private $view;

    /**
     * Mail Instance
     * @var $mail
     */
    private $mail;

    /**
     * Activator Constructor
     * @param Encrypter $encrypter 
     * @param Config    $config
     * @param View      $view      
     * @param Mail      $mail      
     */ 
    public function __construct(
        Encrypter $encrypter, 
        Config $config,
        View $view,
        Mail $mail
    ) {
        $this->encrypter = $encrypter;
        $this->config = $config;
        $this->view = $view;
        $this->mail = $mail;
    }

    /**
     * Activation Process
     * @param  int $userId
     * @return userActivated
     */
    public function activate($user)
    {
        $config = $this->config->get('activator');
        $data = $this->generateData($user->id, $config);

        try {
            $userActivated = UserActivation::create($data);
        } catch (\Exception $e) {
            throw new \Exception('Activate account failed.');
        }

        return $this->sendMailActivation($user);
    }

    /**
     * Generate Data Activation
     * @param  int $userId 
     * @param  array $config 
     * @return array         
     */
    private function generateData(int $userId, array $config)
    {
        $expiryTime = $config['expiry_time'];
        $data = [
            'user_id'       => $userId,
            'token'         => $this->encrypter->encrypt($userId),
            'expires_at'    => Carbon::now()->addMinutes($expiryTime)
        ];

        return $data;
    }

    /**
     * Send Mail Activation
     * @param  Model $user 
     * @return boolean       
     */
    private function sendMailActivation($user)
    {
        return $this->mail->send('activator::activation', ['user' => $user], function ($mail) use ($user) {
            $mail->to($user->email)
                ->subject('Activation Account');
        });
    }

    /**
     * Get routes
     * @param  array  $options 
     * @return Illuminate\Support\Facades\Route          
     */
    public static function routes(array $options = [])
    {
        $options = array_merge($options, [
            'namespace' => '\Rorikurn\Activator\Http\Controllers',
        ]);

        Route::group($options, function ($router) {
            $router->get('/activation', ['uses' => 'ActivationController@index']);
            $router->get('/resend-activation', [
                'uses' => 'ResendActivationController@index'
            ]);
            $router->post('/resend-activation', [
                'uses' => 'ResendActivationController@store'
            ]);
        });
    }
}
