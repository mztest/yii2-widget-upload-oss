/**
 * Created by guoxiaosong on 2016/11/29.
 */
var fileUploadOSS = fileUploadOSS || {};
fileUploadOSS.host = null;
fileUploadOSS.directory = null;
fileUploadOSS.signatureExpire = 0;
fileUploadOSS.formData = {
    key: null,
    policy: null,
    OSSAccessKeyId: null,
    success_action_status: '200', //让服务端返回200,不然，默认会返回204
    callback: null,
    signature: null
};
// Returns the version of Internet Explorer or a -1
// (indicating the use of another browser).
fileUploadOSS.getIEVersion = function () {
    var sAgent = window.navigator.userAgent;
    var Idx = sAgent.indexOf("MSIE");
    var version = 0;

    // If IE, return version number.
    if (Idx > 0) {
        version = parseInt(sAgent.substring(Idx + 5, sAgent.indexOf(".", Idx)));
    } else if (!!navigator.userAgent.match(/Trident\/7\./)) {
    // If IE 11 then look for Updated user agent string.
        version = 11;
    } else {
        version = 0; //It is not IE
    }
    return version;
};
fileUploadOSS.getSignature = function(url, filename) {
    var version = fileUploadOSS.getIEVersion();
    if (version > 0 && version <= 9) {
        $.support.cors = true;
        url += (url.indexOf('?') == '-1') ? '?lowIE=1' : '&lowIE=1';
    }
    //可以判断当前expire是否超过了当前时间,如果超过了当前时间,就重新取一下.3s 做为缓冲
    var now = Date.parse(new Date()) / 1000;
    if (fileUploadOSS.signatureExpire < now + 3) {
        $.ajax({
            url: url,
            dataType: 'json',
            method: 'GET',
            async: false,
            success: function(data) {
                fileUploadOSS.host = data.host;
                fileUploadOSS.directory = data.directory;
                fileUploadOSS.signatureExpire = parseInt(data.expire);
                $.extend(fileUploadOSS.formData, {
                    OSSAccessKeyId: data.accessKeyId,
                    policy: data.policy,
                    signature: data.signature,
                    callback: data.callback || ''
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
    var fullFileName = fileUploadOSS.directory + fileUploadOSS.randomString(32);
    var pos = filename.lastIndexOf('.');
    var suffix = '';
    if (pos != -1) {
        suffix = filename.substring(pos);
    }

    fileUploadOSS.formData.key = fullFileName + suffix;
};