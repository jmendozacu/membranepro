if(!window.Downloads)
    var Downloads=new Object();
Downloads.Methods={
    title:'View',
    width:670,
    overlay:false,
    overlayClose:false,
    autoFocusing:false,
    init:function(options){
        Object.extend(this,options||{});
    },
    open:function(url,params){
        Modalbox.setOptions({
            title:this.title,
            width:this.width,
            overlay:this.overlay,
            overlayClose:this.overlayClose,
            autoFocusing:this.autoFocusing
        });
        Modalbox.show(url,params);
    },
    close:function(){
        Modalbox.hide({
            transitions:true
        });
    }
};

Object.extend(Downloads,Downloads.Methods);