<?php
   
   
   
   
    class FetchReedfotoDefault extends WebPage implements IView {
      
        protected  $template = 'fetchalbum.order';
      
        public function Execute( $imageid, $gruppebildeid = "" ){
            
            $products = array();
            $productpakkearray = array( 3809, 3813 );
            $productarray = array( 3809, 3811, 3799, 3813, 3801, 3815, 3817, 3819, 3843,4542 );
            
            
            $pakker = array(
                            3809 => array(3799, 3801, 3815, 3817, 3819, 3843 ),
                            3811 => array(3799, 3801, 3815, 3817 ),
                            3813 => array(3799, 3801, 3815),
                             );
            $gruppeimageid = explode('_',$gruppebildeid);
            $gruppeimageid = $gruppeimageid[1];
            
            $this->klassebilde = null;
            
            if( $imageid == 'undefined' ){
                $imageid  = $gruppeimageid;
                $productarray = array( 3815 );
                $pakker = array();
                $this->portrettfinns = null;
                 $this->gruppefinns = true;
            }
            else if( $gruppebildeid == 'undefined' ){
                $imageid = explode('_',$imageid);
                $imageid = $imageid[1];
                $productarray = array(  3801, 3817, 3819, 3843, 4542 );
                $pakker = array();
                $this->gruppefinns = null;
                $this->portrettfinns = true;
            }
            else{
                $imageid = explode('_',$imageid);
                $imageid = $imageid[1];
                $this->portrettfinns = true;
                $this->gruppefinns = true;
            }
            
            if( $this->portrettfinns  ){
                $image = new Image( $imageid );
                $this->portrett = $image->asArray();
            }
            
            if( $this->gruppefinns ){
                $image2 = new Image( $gruppeimageid );
                $klassebilde = $image2->asArray();
                $this->klassebilde = $klassebilde;
            }
            
            
            
           
                
            foreach( $productarray as $productoptionid ){
                   
                $productoption = new ProductOption($productoptionid);
                $productid = $productoption->productid;
                $product = new Product($productid);
                $products[$productoptionid] = array(
                            'productoptionid' => $productoptionid,
                            'option' => $productoption->asArray(),
                            'productid' => $productid,
                            'product' => $product->asArray()
                         );
            }
            
            
            foreach( $productpakkearray as $productpakkeoptionid ){
                   
                $productoption = new ProductOption($productpakkeoptionid);

                $productid = $productoption->productid;
                
                $product = new Product($productid);
                
                
                $productspakke[] = array(
                            'productoptionid' => $productpakkeoptionid,
                            'option' => $productoption->asArray(),
                            'productid' => $productid,
                            'product' => $product->asArray()
                         );
                
                
            }
            
            
            $this->pakker = $pakker; 
            $this->products = $products; 
        }
        
        
        public function Norwaycup( $imageid ){
            
            $this->template = 'fetchalbum.ordernc';
            
            $this->portrettfinns = true;
            
            //$productarray = array( 3817, 3819, 3843 );
            $productarray = array( 4360, 4363 );
            
            $image = new Image( $imageid );
            $this->klassebilde = null;
            
            $this->portrett = $image->asArray();
            
            foreach( $productarray as $productoptionid ){
                   
                $productoption = new ProductOption($productoptionid);
                $productid = $productoption->productid;
                $product = new Product($productid);
                $products[$productoptionid] = array(
                            'productoptionid' => $productoptionid,
                            'option' => $productoption->asArray(),
                            'productid' => $productid,
                            'product' => $product->asArray()
                         );
            }
            
            $this->products = $products; 
            
        }
      
      

   } 
?>