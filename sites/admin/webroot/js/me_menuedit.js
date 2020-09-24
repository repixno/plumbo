jQuery.fn.reverse = [].reverse;

var elements = {};

function init() {

   elements.menubox = new TabsBox( '#menulist' );
   elements.menubox.addTab({
      id: 'tabs01',
      title: 'Menu',
      object: new Menu()
      });
   elements.menubox.render();

   elements.activebox = new TabsBox( '#activeitem' );
   elements.activeitem = elements.activebox.addTab({
      id: 'tabs11',
      title: 'Item',
      object: new ActiveItem()
      });
   elements.activebox.render();

   elements.objectsbox = new TabsBox( '#menucontent' );
   elements.objectsbox.addTab({
      id: 'tabs21',
      title: 'Products',
      object: new ObjectList( 'product' )
      });
   elements.objectsbox.addTab({
      id: 'tabs22',
      title: 'Articles',
      object: new ObjectList( 'article' )
      });
   elements.objectsbox.render();



}
