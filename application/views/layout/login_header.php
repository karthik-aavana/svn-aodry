<!DOCTYPE html>
<html>
<head>
    <title></title>
    <style type="text/css">
        .body-login{
            position: static;
            background: #fff;
            box-shadow: 0 2px 6px 0 rgba(0,0,0,.12), inset 0 -1px 0 0 #dadce0;
            transition: transform .4s,background .4s;
            z-index: 100;
            float: left;
            width: 100%;
            height: auto;
            /* margin: 15px; */
            padding: 2px;
            }   
            .body-login img{
                margin: 16px;
                float: left;
                margin-left: auto;
            }
            .body-login .body-log-ul{
                font-size: 17px;
                list-style: none;
                display: -webkit-box;
                margin-top: 10px;
            }
            .body-login .body-log-ul .body-log-sign{
                padding: 0px 40px 0px 0px;
                margin-top: 10px;
            }
            .body-login .body-log-ul .body-log-sign a{
                color: #5f6368;
                margin-top: 10px
            }
            .body-login .body-log-ul .body-log-sign a:hover{
                color: #000000;
            }
            .body-login .body-log-ul .body-log-account{
               /* padding: 0px 15px 0px 30px;*/
                box-shadow: 1px 15px 20px 1px #c1bcbc;
            }
            .body-login .body-log-ul .body-log-account button{
                padding: 0px 20px 0px 20px;
                background-color: #012b72;
                color: #ffffff;
                border: none;
                border-radius: 3px;
                width: 100%;
                /* position: static; */
                height: auto;
                line-height: 3;
                letter-spacing: 1px;

            }
            .body-login .body-log-ul .body-log-account button a{
                color: #ffffff;
            }
            @media screen and (max-width: 600px) {
                .body-login img {
                    margin: 16px;
                    float: left;
                    margin-left: auto;
                    font-size: 10px;
                    width: 100px;
                    height: auto;
                }
            }
            @media screen and (max-width:480px) {
                .body-login img {
                    margin: 21px;
                    float: left;
                    margin-left: auto;
                    font-size: 10px;
                    width: 85px;
                    height: auto;
                }
            }
             @media screen and (max-width:320px) {
                .body-login img {
                    margin: 21px;
                    float: left;
                    margin-left: auto;
                    font-size: 10px;
                    width: 85px;
                    height: auto;
                }
            }
             @media screen and (max-width: 600px) {
                .body-login .body-log-ul{
                   font-size: 10px;
                    list-style: none;
                    display: -webkit-box;
                    margin-top: 10px;
                    padding-left: 0px;
                }
            }
             @media screen and (max-width: 320px) {
            .body-login .body-log-ul .body-log-sign{
                    font-size: 12px;
                    padding-right: 16px;
                }
            }
             @media screen and (max-width: 480px) {
            .body-login .body-log-ul .body-log-sign{
                    font-size: 15px;
                    padding-right: 16px;
                }
            }
           @media screen and (max-width: 320px) {
            .body-login .body-log-ul .body-log-account button {
                  padding: 0px 5px 0px 5px;
                  font-size: 4px;
                  margin-top: 5px;
                }
            }
            @media screen and (max-width: 600px) {
                .body-login .body-log-ul .body-log-account button {
                      padding: 0px 5px 0px 5px;
                      font-size: 9px;
                      margin-top: 5px;
                }
            }
             @media screen and (max-width: 768px) {
                .body-login .body-log-ul .body-log-account button {
                         font-size: 11px;
                         margin-top: 5px; 
                        padding: 0px 5px 0px 5px;
                    }
                }
            }                            
    </style>
</head>
<body>
     <div class="body-login">
            <div class="col-sm-12">
                <div class="col-sm-7">
                    <img src="<?= base_url('assets/images/aodry-black-logo.png') ?>" width="130px" height="40px" style="">
                </div>
                <div class="col-sm-5">
                    <ul class="body-log-ul pull-right">
                        <li class="body-log-sign" style="">
                            <a href="login">Sign in</a>
                        </li>
                        <a href="signup"><li class="body-log-account" style="">
                            <button>
                                Create an Account
                            </button> 
                        </li></a>
                    </ul>
                </div>
            </div>
        </div> 
</body>
</html>