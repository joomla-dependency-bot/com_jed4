/* jce - 2.8.3 | 2020-01-15 | https://www.joomlacontenteditor.net | Copyright (C) 2006 - 2020 Ryan Demmer. All rights reserved | GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html */
!function(){var DOM=tinymce.DOM;tinymce.dom.Event;tinymce.create("tinymce.plugins.SourcePlugin",{init:function(ed,url){var self=this;self.editor=ed,ed.plugins.fullscreen&&(ed.onFullScreen.add(function(ed,state){var element=ed.getElement(),header=(element.parentNode,DOM.getPrev(element,".wf-editor-header"));if(state){var vp=DOM.getViewPort();DOM.setStyle(element,"height",vp.h-header.offsetHeight)}else DOM.setStyle(element,"height",ed.settings.container_height)}),ed.onFullScreenResize.add(function(ed,vp){var element=ed.getElement();DOM.setStyle(element,"height",vp.h)})),ed.onInit.add(function(ed){var activeTab=sessionStorage.getItem("wf-editor-tabs-"+ed.id)||ed.settings.active_tab||"";if("wf-editor-source"===activeTab){ed.hide(),DOM.show(ed.getElement());var element=ed.getElement(),container=element.parentNode,width=ed.settings.container_width||sessionStorage.getItem("wf-editor-container-width");DOM.hasClass(container,"mce-fullscreen")||DOM.setStyle(element,"width",width)}})},insertContent:function(v){var ed=this.editor,el=ed.getElement();if(document.selection){el.focus();var s=document.selection.createRange();s.text=v}else if(el.selectionStart||"0"==el.selectionStart){var startPos=el.selectionStart,endPos=el.selectionEnd;el.value=el.value.substring(0,startPos)+v+el.value.substring(endPos,el.value.length)}else el.value+=v},getContent:function(){var ed=this.editor;return ed.getElement().value},save:function(){return this.getContent()},hide:function(){var ed=this.editor;DOM.hide(ed.getElement())},toggle:function(){var ed=this.editor,element=ed.getElement(),container=element.parentNode,header=DOM.getPrev(element,".wf-editor-header"),ifrHeight=parseInt(DOM.get(ed.id+"_ifr").style.height)||s.height,o=tinymce.util.Cookie.getHash("TinyMCE_"+ed.id+"_size");o&&o.height&&(ifrHeight=o.height),DOM.isHidden(element)?(DOM.show(element),DOM.removeClass(container,"mce-loading")):DOM.hide(element);var height=ed.settings.container_height||sessionStorage.getItem("wf-editor-container-height")||ifrHeight;if(DOM.hasClass(container,"mce-fullscreen")){var vp=DOM.getViewPort();height=vp.h-header.offsetHeight}DOM.setStyle(element,"height",height);var width=ed.settings.container_width||sessionStorage.getItem("wf-editor-container-width");DOM.hasClass(container,"mce-fullscreen")||DOM.setStyle(element,"width",width)},getCursorPos:function(){return 0}}),tinymce.PluginManager.add("source",tinymce.plugins.SourcePlugin)}();