<template>
    <div>
        <input type="checkbox" id="checkbox">
        <header class="header">
            <img :src="image_logo" id="leftSideLogo">
            <label for="checkbox">
                <i id="navbtn" class="fa fa-bars" aria-hidden="true"/>
            </label>
            <label id="userZoneDashboard">
                <div class="userNameZone">
                    <div>
                        <span class="welcomeUserTitle">Xin chào</span>
                    </div>
                    <div>
                        <span id="userNameDisplay">Nguyen Tien Nam</span>
                    </div>
                </div>

                <img :src="image_logo" id="avatarUser" class="avatarUser">

            </label>
        </header>
        <div class="body">
            <nav class="side-bar">
                <div class="side">
                    <ul>
                        <li>
                            <a href="#" class="titleOption">
                                <i class="far fa-building" style="font-size: 15px"/>
                                <span>Đặt phòng họp</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="titleOption">
                                <i class="far fa-user-circle" style="font-size: 15px"/>
                                <span>Quản lý người dùng</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="titleOption" style="color: orange">
                                <i class="far fa-bookmark" style="font-size: 15px"/>
                                <span>Quản lý phòng họp</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="titleOption">
                                <i class="far fa-calendar" style="font-size: 15px"/>
                                <span>Lịch họp của tôi</span>
                            </a>
                        </li>

                        <!--                <li>-->
                        <!--                    <a href="#" class="titleOption">-->
                        <!--                        <i class="fa fa-power-off" aria-hidden="true" style="font-size: 18px"></i>-->
                        <!--                        <span >Đăng xuất</span>-->
                        <!--                    </a>-->
                        <!--                </li>-->
                    </ul>
                </div>
            </nav>

            <section class="section-1">

                <div class="selectBar">
                    <a href="">Quản lý phòng họp ></a>

                    <span><b>Tạo mới phòng họp</b></span>
                </div>
                <form  @submit.prevent="createMeeting" id="createForm" enctype="multipart/form-data">
                    <div class="formCreateMeeting">
                        <div class="formCreateContent">
                            <p><b>Thông tin cơ bản</b></p>
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="btn-upload-image-meeting" id="imgBackground">
                                        + Upload <input @change="previewImg" type="file" id="previewImgCreate" name="image"  style="display: none;">
                                    </label>
                                </div>
                                <div class="col-8">
                                    <span><b><span style="color: red">*</span> Chọn ảnh đại diện cho phòng họp</b></span>
                                    <br>
                                    <span class="desImg">Kích thước đề xuất:</span>
                                    <br>
                                    <span class="desImg">640x360 Max file size: 15MB</span>
                                </div>
                            </div>

                            <div class="infoMeetingForm">
                                <div class="row">
                                    <div class="col-2">
                                        <span><b><span style="color: red">*</span>Tên phòng họp:</b></span>
                                    </div>
                                    <div class="col-4">
                                        <input type="text" class="form-control" name="name" placeholder="Nhập tên phòng họp" v-model="createData.name">
                                    </div>
                                    <div class="col-2">
                                        <span><b><span style="color: red">*</span>Chọn địa chỉ:</b></span>
                                    </div>
                                    <div class="col-4">
                                        <select class="form-control" name="location_id" v-model="createData.location_id">
                                            <option v-for="locate in locations" :value="locate.id">{{locate.name}}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row row2">
                                    <div class="col-2">
                                        <span><b><span style="color: red">*</span>Số lượng người:</b></span>
                                    </div>
                                    <div class="col-4">
                                        <input class="form-control" type="number" name="capacity" v-model="createData.capacity" placeholder="Nhập số lượng người tối đa">
                                    </div>
                                </div>

                                <div class="row row2">
                                    <div class="col-2">
                                        <span><b><span style="color: red">*</span>Mô tả:</b></span>
                                    </div>
                                    <div class="col-10">
                                        <textarea  class="form-control" name="description"  rows="3" placeholder="Nhập mô tả phòng họp"
                                                  v-model="createData.description" style=" resize: none;"/>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <button  type="submit"  class="btnCreateMeeting">Tạo mới</button>
                    </div>
                </form>
            </section>
        </div>
    </div>


</template>

<script>
    export default {
        name: "CreateMeetingComponent",

        data(){
            return {
                createData: new FormData(),
                image_logo: '/storage/images/logo.png',
                locations : [],
                image: null
            }
        },
        methods: {
            previewImg(e){
                let file = e.target.files[0];
                this.image = e.target.files[0];
                let reader = new FileReader();
                reader.onloadend = function () {
                    //id cua the label
                    $('#imgBackground').css('background-image', 'url("' + reader.result + '")');
                };
                if (file) {
                    reader.readAsDataURL(file);
                } else {
                }
            },
            createMeeting(){
                let uri = 'http://127.0.0.1:8000/api/meeting/create';

                this.createData.append('name',this.createData.name);
                this.createData.append('description',this.createData.description);
                this.createData.append('location_id',this.createData.location_id);
                this.createData.append('capacity',this.createData.capacity);
                this.createData.append('image', this.image);
                this.axios.post(uri, this.createData).then((response) => {
                   toastr.success(response.data.message);
                }).catch(function(error){
                    console.log(error.response);
                    if (error.response.data.message.capacity){
                        toastr.error(error.response.data.message.capacity[0]);
                    }
                    if (error.response.data.message.image){
                        toastr.error(error.response.data.message.image[0]);
                    }
                })
            },

        },
        created() {
            let uri = 'http://127.0.0.1:8000/api/location';
            this.axios.get(uri).then(response => {
                this.locations = response.data.data;
            });
        },
    }

    // preview img trong label


</script>

<style>
    .selectBar{
        margin-left: 90px;
        margin-top: 3%;
        position: relative;
        width: 100%;
    }

    .formCreateMeeting{
        box-shadow: rgba(0, 0, 0, 0.1) 0 5px 40px, rgba(0, 0, 0, 0.1) 0 5px 10px;
        width: 1080.94px;
        height: 421px;
        left: 90px;
        top: 20px;
        position: relative;
        border: 1px solid #EDF2F9;
    }
    .formCreateContent{
        margin: 10px;
    }

    .btn-upload-image-meeting{
        margin-left: 25%;
        width: 82px;
        height: 82px;
        background-color: #EDF2F9;
        background-size: cover;
        background-position: center;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        font-size: 12px;
        color: #72849A;
        cursor: pointer;
    }

    .btn-upload-image-meeting:hover{
        background-color: #d8dde3;
    }

    .desImg{
        color: #72849A;
        font-size: 12px;
    }
    .infoMeetingForm{
        margin-top: 10px;

    }

    .createMeetingInput{
        margin-top: 5%;
    }
    .row2{
        margin-top: 2%;
    }

    .btnCreateMeeting{
        width: 129px;
        height: 45px;
        position: relative;
        border: none;
        background-color: #d8dce2;
        box-shadow: rgba(0, 0, 0, 0.1) 0 5px 40px, rgba(0, 0, 0, 0.1) 0 5px 10px;
        color: #546171;
        margin-top: 5%;
        margin-left: 50%;
    }

    .btnCreateMeeting:hover{
        background-color: #bbbbbb;
    }



    * {
        padding: 0;
        margin: 0;
        box-sizing: border-box;
        font-family: arial;
    }


    .welcomeUserTitle{
        color: white;
        font-style: italic;
        font-size: 12px;
        margin-left: 80px;

    }


    .userNameZone{
        float: left;
        /*left: 400px;*/
    }

    /*@media #userZoneDashboard  {*/
    /*    */
    /*}*/
    #userZoneDashboard{
        position: fixed;
        float: right;
        margin-left: 75%;
        width: 100%;
        margin-right: 5%;
        /*min-width: 500px;*/
    }
    #userZoneDashboard img:hover{
        opacity: 0.8;
    }

    #userZoneDashboard img{
        float: left;
        display: inline-block;
        margin-left: 7px;
        /*padding-bottom: 50px;*/
        top: 9px;
        position: relative;
        border-radius: 50%;
        width: 36px;
        height: 36px;
        opacity: 1;
        background-color: white;
        box-shadow: rgba(0, 0, 0, 0.1) 0 5px 40px, rgba(0, 0, 0, 0.1) 0 5px 10px;
        border: solid 2px white;
    }

    /*Bat DAU NAV BAR*/
    #leftSideLogo{
        width: 113px;
        height: 50px;
        left: 0px;
        top: 0px;
        position: absolute;


    }

    .side{
        background-color: #011528;
        margin-top: 0;
        /*top: -20px*/
        /*height: 500px;*/
    }

    .totalRoom{
        width: 100%;
        height: 100%;
    }
    .titleMeetingRoom{
        font-weight: bold;
        position: relative;
        margin-top: 10px;
        margin-left: 15px;
    }
    .totalRoomFilter{

    }
    .totalRoom .totalRoomFilter{
        float: left;
        margin-left: 70%;

    }

    /*.totalRoomFilter a{*/
    /*    text-space: 2px;*/
    /*}*/


    .header {
        display: flex;
        /*justify-content: space-between;*/
        align-items: center;
        padding: 10px 30px;
        background: #253D7F;
        color: #fff;
        height: 50px;
        /*width: 100%;*/
        min-width: 110%;

    }
    .u-name {
        font-size: 20px;
        padding-left: 17px;
    }
    .u-name b {
        color: #127b8e;
    }
    .header i {
        font-size: 30px;
        cursor: pointer;
        color: #fff;

    }
    .header i:hover {
        color: #127b8e;
    }
    .user-p {
        text-align: center;
        padding-left: 10px;
        padding-top: 25px;
    }
    /*.user-p img {*/
    /*    width: 100px;*/
    /*    border-radius: 50%;*/
    /*}*/
    .user-p h4 {
        color: #ccc;
        padding: 5px 0;

    }
    .titleOption{
        font-size: 14px;
    }

    /*bắt đầu side bar*/
    .side-bar {
        width: 196px;

        background: #233143;
        min-height: 100vh;
        transition: 500ms width;
    }
    .body {
        display: flex;
    }

    /*Nội dung trong section*/
    .section-1 {
        width: 100%;
        /*background: url("../img/bg.jpg");*/
        /*background-size: cover;*/
        /*background-position: center;*/
        /*display: flex;*/
        /*justify-content: center;*/
        /*align-items: center;*/
        /*flex-direction: column;*/
    }
    .section-1 h1 {
        color: #fff;
        font-size: 60px;
    }
    .section-1 p {
        color: #127b8e;
        font-size: 20px;
        background: #fff;
        padding: 7px;
        border-radius: 5px;
    }
    .side-bar ul {

        list-style: none;
    }
    .side-bar ul li {

        font-size: 12px;
        /*dãn cách thẻ bar*/
        padding: 15px 0px;

        padding-left: 20px;
        transition: 500ms background;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .side-bar ul li:hover {
        background: #127b8e;
    }
    .side-bar ul li a {
        text-decoration: none;
        color: #eee;
        cursor: pointer;
        letter-spacing: 1px;
    }
    .side-bar ul li a i {
        display: inline-block;
        padding-right: 10px;
        font-size: 23px;
    }
    #navbtn {
        display: inline-block;
        margin-left: 120px;
        font-size: 20px;
        transition: 500ms color;
        margin-top: 10%;
    }
    #checkbox {
        display: none;
    }
    #checkbox:checked ~ .body .side-bar {
        width: 60px;
    }
    #checkbox:checked ~ .body .side-bar .user-p{
        visibility: hidden;
    }
    #checkbox:checked ~ .body .side-bar a span{
        display: none;
    }

    .optionI{
        font-size: 15px;
    }

</style>
