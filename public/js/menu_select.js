                                    
function menu_select(page_url)
{
    var items  = document.getElementsByTagName("li");
    

    for(var i=0; i<items.length; i++)
    {
            var children = items[i].children;

            for(var a=0;a<children.length; a++)
            {

                    if(children[a].tagName=="A" && children[a].href===page_url)
                    {

                            children[a].className = "mm-active";
                            var parent = items[i].parentElement;
                            if(parent.getAttribute('class')=='vertical-nav-menu')
                            {
                                
                            }
                            else
                            {
                                parent.setAttribute("class","mm-show");
                                var grand_parent = parent.parentElement;
                                grand_parent.setAttribute("class","mm-active");
                            }
                    }

            }

    } 

}
