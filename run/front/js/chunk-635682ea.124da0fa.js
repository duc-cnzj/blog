(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-635682ea"],{"0abc":function(t,e,a){"use strict";a.r(e);var n=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{staticClass:"page_content"},[a("div",{staticClass:"container"},[a("div",{staticClass:"row row-lg-eq-height"},[a("div",{staticClass:"col-lg-9 offset-lg-1"},[t.articles.length>0?a("div",{staticClass:"section_content"},[a("div",{staticClass:"card-deck-wrapper duc"},t._l(t.articles,function(t){return a("div",{key:t.id,staticClass:"card-deck"},[a("largest-card-with-image",{attrs:{image:t.headImage,path:"/articles/"+t.id,title:t.title,author:t.author.name,author_url:"/authors/"+t.author.id,created_at:t.created_at,desc:t.desc}})],1)})),a("paginator",{attrs:{dataSet:t.dataSet},on:{changed:t.changed}}),a("hr")],1):a("div",{staticClass:"section_content"},[a("loading")],1)])])])])},i=[],r=(a("f751"),a("96cf"),a("3040")),s=(a("cadf"),a("551c"),a("097d"),a("8045")),c=a("3a5e"),o=function(){var t=this,e=t.$createElement,a=t._self._c||e;return t.shouldPaginate?a("nav",{attrs:{"aria-label":"Page navigation example"}},[a("ul",{staticClass:"pagination justify-content-center"},[a("li",{directives:[{name:"show",rawName:"v-show",value:t.prevUrl,expression:"prevUrl"}],staticClass:"page-item",on:{click:function(e){t.page--}}},[a("span",{staticClass:"page-link",attrs:{href:"#",tabindex:"-1"}},[t._v("👈")])]),a("li",{directives:[{name:"show",rawName:"v-show",value:t.nextUrl,expression:"nextUrl"}],staticClass:"page-item",on:{click:function(e){t.page++}}},[a("span",{staticClass:"page-link",attrs:{href:"#"}},[t._v("👉")])])])]):t._e()},l=[],u={props:["dataSet"],data:function(){return{page:1,prevUrl:"",nextUrl:""}},watch:{dataSet:function(){this.page=this.dataSet.current_page,this.prevUrl=this.dataSet.prev,this.nextUrl=this.dataSet.next},page:function(){this.broadcast().updateUrl()}},created:function(){this.page=this.dataSet.current_page,this.prevUrl=this.dataSet.prev,this.nextUrl=this.dataSet.next},computed:{shouldPaginate:function(){return!!this.prevUrl||!!this.nextUrl}},methods:{broadcast:function(){return this.$emit("changed",this.page)},updateUrl:function(){}}},d=u,h=(a("4539"),a("2877")),f=Object(h["a"])(d,o,l,!1,null,"a7c53afc",null);f.options.__file="Paginator.vue";var p=f.exports,g=a("4ec3"),v={components:{LargestCardWithImage:s["a"],Paginator:p,Loading:c["a"]},data:function(){return{articles:[],dataSet:{}}},created:function(){this.fetchArticles()},methods:{changed:function(t){this.fetchArticles(t)},fetchArticles:function(){var t=Object(r["a"])(regeneratorRuntime.mark(function t(e){var a;return regeneratorRuntime.wrap(function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,Object(g["b"])(e);case 2:a=t.sent,this.articles=a.data,this.dataSet=Object.assign(a.links,a.meta);case 5:case"end":return t.stop()}},t,this)}));return function(e){return t.apply(this,arguments)}}()}},m=v,b=(a("8602"),Object(h["a"])(m,n,i,!1,null,"d95718c8",null));b.options.__file="ArticleContent.vue";e["default"]=b.exports},2621:function(t,e){e.f=Object.getOwnPropertySymbols},4539:function(t,e,a){"use strict";var n=a("aff9"),i=a.n(n);i.a},"52a7":function(t,e){e.f={}.propertyIsEnumerable},7333:function(t,e,a){"use strict";var n=a("0d58"),i=a("2621"),r=a("52a7"),s=a("4bf8"),c=a("626a"),o=Object.assign;t.exports=!o||a("79e5")(function(){var t={},e={},a=Symbol(),n="abcdefghijklmnopqrst";return t[a]=7,n.split("").forEach(function(t){e[t]=t}),7!=o({},t)[a]||Object.keys(o({},e)).join("")!=n})?function(t,e){var a=s(t),o=arguments.length,l=1,u=i.f,d=r.f;while(o>l){var h,f=c(arguments[l++]),p=u?n(f).concat(u(f)):n(f),g=p.length,v=0;while(g>v)d.call(f,h=p[v++])&&(a[h]=f[h])}return a}:o},8602:function(t,e,a){"use strict";var n=a("b46e"),i=a.n(n);i.a},aff9:function(t,e,a){},b46e:function(t,e,a){},f751:function(t,e,a){var n=a("5ca1");n(n.S+n.F,"Object",{assign:a("7333")})}}]);
//# sourceMappingURL=chunk-635682ea.124da0fa.js.map