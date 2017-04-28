<?php $data = require 'charge.php';?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>招商支付</title>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport">
    <meta content="telephone=no" name="format-detection">
    <style>
        .box{
            padding:6px 10px
        }
        .button {
            color: #f5efef;
            background-color: #10a737;
            border-color: #EEE;
            font-weight: 300;
            font-size: 16px;
            font-family: "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
            text-decoration: none;
            text-align: center;
            line-height: 40px;
            height: 100px;
            padding: 0 40px;
            margin: 0;
            width: 100%;
            display: inline-block;
            appearance: none;
            cursor: pointer;
            border: none;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
            -webkit-transition-property: all;
            transition-property: all;
            -webkit-transition-duration: .3s;
            transition-duration: .3s;
        }
        .button-rounded {
            border-radius: 4px;
        }
        .button-uppercase {
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="box">
        <form method="post" action="<?php echo $data['url'] ?>">
            <input type="hidden" name="<?php echo $data['name'] ?>" value='<?php echo $data['value'] ?>'>
            <button type="submit" class="button button-rounded button-uppercase">点我开始支付</button>
        </form>
    </div>
</body>
</html>