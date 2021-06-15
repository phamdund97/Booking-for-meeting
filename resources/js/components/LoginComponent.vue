<template>
    <div >
    <img :src="image_logo" class="leftSideLogo"/>
    <div class="dashboardClient">
        <form id="loginForm"  @submit.prevent="userLogin">
            <!--    form title-->
            <div class="titleForm">
                <h3 class="titleLoginForm"><b>Đăng nhập</b></h3>
                <label class="subtitleLoginForm">Nhập tài khoản và mật khẩu để đăng nhập hệ thống</label>
            </div>

            <!--    end of form title-->
            <div class="form-content">
                <div class="forUserName">
                    <label><b><span class="d">*</span>Tài khoản</b></label>
                    <input placeholder="Nhập Email TTC/ @ttc-solutions.com.vn" type="email"  id="checkMailTTC" name="email" class="emailInput" v-model="loginData.email"><i id="a" class="fa fa-user"></i>
                    <span  class="validateEmail" ></span>
                </div>

                <div class="forUserPassword">
                    <label><b><span class="d">*</span>Mật khẩu</b></label>
                    <i class="far fa-eye" @click="showPassword" id="togglePassword"></i> <input placeholder="Nhập mật khẩu" type="password" name="password" id="passwordInput" v-model="loginData.password"><i class="fas fa-lock"></i>
                    <div class="msgZone">
                        <span class="MsgEmail" style="color:red"></span>
                        <br>
                        <span class="MsgPassword" style="color:red;"></span>
                    </div>
                </div>
                <button type="submit" id="btnLogin" >Đăng nhập</button>
            </div>
        </form>
        <!--    img on the right side-->
        <img :src="image_backgroundSide" class="rightBackgroundDoor">
    </div>
</div>
</template>
<script>
    export default {
        data(){
            return {
                image_logo: '/storage/images/logo.png',
                image_backgroundSide: '/storage/images/loginTheme.svg',
                loginData:{},
            }
        },
        methods: {
            userLogin(){
                let uri = process.env.MIX_URI_LOGIN;
                this.axios.post(uri, this.loginData).then((response) => {
                    this.$router.push({name: 'home'});
                }).catch(function(error){
                    $('.MsgEmail').html("");
                    if (error.response.data.message.email){
                        $('.MsgEmail').text(error.response.data.message.email[0]);
                    }
                    if(error.response.data.message.password) {
                        $('.MsgPassword').text(error.response.data.message.password[0]);
                    }
                    if (error.response.status === 400 ){
                        $('.MsgPassword').text('Sai tài khoản hoặc mật khẩu!');
                    }
                });
            },

            //TTC validate zone
            //Tạm thời chưa cần, khi nào cần thì uncomment
            // ttcValidateMail(){
            //     let ttcValidate = /^[a-zA-Z0-9]+@ttc-solutions.com.vn$/;
            //     let emailInput = $('#checkMailTTC').val();
            //     if (ttcValidate.test(emailInput)){
            //         $('.validateEmail').removeClass('invalidMailTTC');
            //         $('.validateEmail').addClass('validMailTTC');
            //         $('.validateEmail').text('Đây là mail của TTC!');
            //     }
            //     else {
            //         $('.validateEmail').removeClass('validMailTTC');
            //         $('.validateEmail').addClass('invalidMailTTC');
            //         $('.validateEmail').text('Đây không phải mail của TTC!');
            //     }
            // }
            //end of TTC validate


            showPassword(){
                const togglePassword = document.querySelector('#togglePassword');
                const password = document.querySelector('#passwordInput');
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
            
            }
        }
    }
</script>
<style>
    .validMailTTC{
        color: green;
    }
    .invalidMailTTC{
        color: red;
    }

    .errorsServerEmail{
        color: red;
    }
    .errorsServerPassword{
        color: red;
    }
    .getErrorEmail{
        border: 2px solid red;
        border-radius: 4px;
    }
</style>

