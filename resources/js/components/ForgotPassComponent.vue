<template>


    <div >
        <img :src="image_logo" class="leftSideLogo"/>
        <div class="dashboardClient">
            <!--START FORGOT PASSWORD HTML-->
            <div id="forgotPasswordDiv">
                <div class="titleForm">
                    <h3 class="titleLoginForm"><b>Quên mật khẩu?</b></h3>
                    <label class="subtitleLoginForm">Nhập Email của bạn để hệ thống kiểm tra</label>
                </div>

                <!--        start form forgot password-->

                <div class="form-content" @submit.prevent="forgotPass">
                    <div class="forUserForgotPassword">
                        <label><b>Nhập Mail của bạn</b></label>
                        <!--            form forgot pass-->
                        <form method="post" action="">
                            <input placeholder="Nhập Email TTC/ @ttc-solutions.com.vn" type="email" id="forgotPasswordInput"  name="forgotEmail" oninput="validateMailTtc(this.value)" class="emailInput"><i id="forgotI" class="fa fa-user"></i>
                            <span  class="validateEmail"></span>
                            <button id="sendMailBtn" type="button">Gửi Mail <i class="far fa-envelope"></i></button>
                        </form>

                    </div>
                    <label id="returnToLogin"> <router-link to="/login">Quay về trang chủ</router-link></label>
                </div>

                <!--end of forgot password-->
<!--                <img src="img/forgotPassword.svg" class="rightBackgroundForgotPassword">-->
            </div>
            <!--END OF FORGOT PASSWORD HTML-->

            <!--    img on the right side-->
            <img :src="image_backgroundSide" class="rightBackgroundForgotPassword">
        </div>
    </div>
</template>

<script>
    export default {
        data(){

            return {
                image_logo: '/storage/images/logo.png',
                image_backgroundSide: '/storage/images/forgotPassword.svg',
                loginData:{},

            }
        },
        methods: {
            forgotPass(){
                let uri = 'http://127.0.0.1:8000/api/login';
                this.axios.post(uri, this.loginData).then((response) => {
                    this.$router.push({name: 'login'});
                });
            }
        }
    }
</script>

<style scoped>

</style>
