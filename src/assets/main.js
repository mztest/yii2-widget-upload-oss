/**
 * Created by guoxiaosong on 2016/11/29.
 */
var fileUploadOSS = fileUploadOSS || {};
fileUploadOSS.signatureExpire = 0;
fileUploadOSS.formData = {
    host: null,
    key: null,
    policy: null,
    OSSAccessKeyId: null,
    success_action_status: '200', //让服务端返回200,不然，默认会返回204
    callback: null,
    signature: null
};
fileUploadOSS.getSignature = function(url, filename) {
    //可以判断当前expire是否超过了当前时间,如果超过了当前时间,就重新取一下.3s 做为缓冲
    var now = Date.parse(new Date()) / 1000;
    if (fileUploadOSS.signatureExpire < now + 3) {
        $.ajax({
            url: url,
            dataType: 'json',
            method: 'GET',
            async: false,
            success: function(data) {
                fileUploadOSS.signatureExpire = parseInt(data.expire);
                $.extend(fileUploadOSS.formData, {
                    OSSAccessKeyId: data.accessKeyId,
                    policy: data.policy,
                    signature: data.signature,
                    callback: data.callback,
                    dir: data.dir,
                    host: data.host
                });
            }
        });
    }
    fileUploadOSS.generateObjectKey(filename);
};
fileUploadOSS.randomString = function(len) {
    len = len || 32;
    var chars = 'abcdefhijkmnprstwxyz2345678';
    var maxPos = chars.length;
    var str = '';
    for (var i = 0; i < len; i++) {
        str += chars.charAt(Math.floor(Math.random() * maxPos));
    }
    return str;
};
fileUploadOSS.generateObjectKey = function(filename) {
    var fullFileName = fileUploadOSS.formData.dir + fileUploadOSS.randomString(32);
    var pos = filename.lastIndexOf('.');
    var suffix = '';
    if (pos != -1) {
        suffix = filename.substring(pos);
    }

    fileUploadOSS.formData.key = fullFileName + suffix;
};