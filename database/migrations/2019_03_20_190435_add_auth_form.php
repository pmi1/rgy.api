<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAuthForm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('cmf_script')
            ->where('cmf_script_id', 348)
            ->where('cmf_site_id', 1)
            ->update(['catname' => 'login'
                , 'realcatname' => 'login'
                , 'url' => '']);

        DB::table('xmls')
            ->where('xmls_id', 348)
            ->where('cmf_site_id', 1)
            ->where('type', 0)
            ->update(['xml' => '<div class="auth">
          
          <form class="auth__form auth__form_auth" id="login-form" action="/cabinet/orders/">
            <div class="auth__title">
              Авторизация
            </div>
            
            <div class="auth__line">
              <input class="text-input" name="email"
                     type="email"
                     placeholder="Ваш E-Mail"/>
            </div>
            
            <div class="auth__line">
              <input class="text-input" name="pass"
                     type="password"
                     placeholder="Пароль"/>
            </div>
            
            <label class="auth__remember">
              <input class="check" type="checkbox"
                     name="is_save_me" value="1"
                     checked="checked"
                     id="save_me"/>
              <span class="label-text">Запомнить меня</span>
            </label>
    
            <button class="auth__button red-button"
                    type="submit">
              Войти
            </button>
            
            <a href="#" class="auth__recovery modal-trigger"
               data-target="recovery-modal" rel="nofollow">
              Забыли пароль?
            </a>
            
            <a class="auth__trigger auth__trigger_auth" href="#">
              Регистрация
            </a>
          </form>
  
          <form class="auth__form auth__form_reg visually-hidden" id="signin-form">
            <div class="auth__title">
              Регистрация
            </div>
            
            <p class="auth__text">
              Введите свой E-mail, и мы пришлем Вам пароль на почту
            </p>
  
            <div class="auth__line">
              <input class="text-input low-input" name="email"
                     type="email"
                     placeholder="Ваш E-Mail"/>
            </div>
    
            <button class="auth__button red-button"
                    type="submit">
              Зарегистрироваться
            </button>
  
            <a class="auth__trigger auth__trigger_reg" href="#">
              Авторизация
            </a>
          </form>
        </div>']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('cmf_script')
            ->where('cmf_script_id', 348)
            ->where('cmf_site_id', 1)
            ->update(['catname' => ''
                , 'realcatname' => ''
                , 'url' => '/user/login/']);

        DB::table('xmls')
            ->where('xmls_id', 348)
            ->where('cmf_site_id', 1)
            ->where('type', 0)
            ->update(['xml' => '']);
    }
}
