(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-979209ca"],{"5a75":function(t,a,e){},"935a":function(t,a,e){"use strict";var o=e("5a75"),s=e.n(o);s.a},c3bd:function(t,a,e){"use strict";e.r(a);var o=function(){var t=this,a=t.$createElement,e=t._self._c||a;return e("div",[e("Card",{attrs:{bordered:!1}},[e("Tabs",[e("TabPane",{attrs:{label:"基本设置",icon:"ios-hammer-outline"}},[e("Row",[e("Col",{attrs:{span:"16"}},[e("Form",{staticStyle:{width:"80%"},attrs:{model:t.formItem,"label-width":180}},[e("FormItem",{attrs:{label:"昵称"}},[e("Input",{attrs:{placeholder:"your name.."},model:{value:t.formItem.name,callback:function(a){t.$set(t.formItem,"name",a)},expression:"formItem.name"}})],1),e("FormItem",{attrs:{label:"Bio"}},[e("Input",{attrs:{type:"textarea",autosize:{minRows:2,maxRows:5},placeholder:"you are not alone."},model:{value:t.formItem.bio,callback:function(a){t.$set(t.formItem,"bio",a)},expression:"formItem.bio"}})],1),e("FormItem",{attrs:{label:"邮箱地址📮"}},[e("Input",{attrs:{placeholder:"example@xxx.com"},model:{value:t.formItem.email,callback:function(a){t.$set(t.formItem,"email",a)},expression:"formItem.email"}})],1),e("FormItem",[e("Button",{attrs:{type:"primary"},on:{click:t.onSubmit}},[t._v("保存")]),e("Button",{staticStyle:{"margin-left":"8px"},on:{click:t.reset}},[t._v("重置")])],1)],1)],1),e("Col",{attrs:{span:"8"}},[e("div",{staticClass:"ant-upload-preview",on:{click:function(a){t.$refs.modal.edit()}}},[e("Icon",{staticClass:"upload-icon",attrs:{type:"ios-cloud-upload-outline"}}),e("div",{staticClass:"mask"},[e("Icon",{attrs:{type:"md-add"}})],1),e("img",{attrs:{src:t.$store.state.user.avatorImgPath}})],1)]),e("avatar-modal",{ref:"modal",attrs:{avatarSrc:t.$store.state.user.avatorImgPath},on:{"avatar-onsave":t.avatarSave,"avatar-oncancel":t.avatarCancel}})],1)],1),e("TabPane",{attrs:{label:"todo1",icon:"logo-windows"}},[t._v("待定1")]),e("TabPane",{attrs:{label:"todo2",icon:"logo-tux"}},[t._v("待定2")])],1)],1)],1)},s=[],r=(e("f751"),e("4428")),n=e("9dcf"),i={name:"userCenter",components:{UploadImage:r["a"],AvatarModal:n["a"]},data:function(){return{formItem:{name:"",bio:"",email:"",avatar:null}}},created:function(){this.setUser()},methods:{avatarSave:function(t){var a=this;console.log(t),this.$store.dispatch("setAvatar",t).then(function(){a.$Message.success("头像更新成功！")})},avatarCancel:function(){},setUser:function(){this.formItem=Object.assign({},this.$store.state.user)},reset:function(){this.setUser()},onSubmit:function(){var t=this;this.$store.dispatch("UpdateInfo",this.formItem).then(function(){t.$Message.success("用户信息更新成功！")}).catch(function(a){console.log(a),t.$Message.error("用户信息更新失败！")})}}},l=i,c=(e("935a"),e("2877")),m=Object(c["a"])(l,o,s,!1,null,"dc9e93c0",null);m.options.__file="UserSetting.vue";a["default"]=m.exports}}]);