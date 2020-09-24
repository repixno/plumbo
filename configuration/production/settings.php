<?PHP
   
   Settings::Set( 'production', 'imageserver', array(
		 'imageserver' => "http://therese.eurofoto.no/production/index.php",
		 'securecode' => "p-dLuxTl30qH-AhKICmehN2DalJsJNGjjAGrzom1DNQ" 										 
	  
	  ));
   
   
  // Settings::Set( 'production', 'fileserver', 'monica.eurofoto.no' );
   Settings::Set( 'production', 'fileserver', 'zfs.eurofoto.no' );
   
   Settings::Set( 'production', 'printsize' , array(
						'default_print' => array(1, 2, 419, 14, 7345, 6, 1523),
						'size_20x25' => array(3),
						'size_20x30' => array(439),
						'size_30x25' => array(4),
						'size_30x45' => array(7461),
						'size_30x40' => array(5, 7398, ),
						'size_15x20' => array(7347,2015),
                  'utestemme' => array(522),
   
	
      )
   
   );
   
   Settings::Set('production', 'malprintsize', array(
   		'size_10x9_cnv' => array(557, 675),
   		'size_10x9_globe' => array(693, 7254, 7551),
   		'size_10x18' => array(6, 7, 9, 129, 558, 559, 560, 561, 564, 571, 873, 939, 6038,7298),
   		'size_10x15_cnv' => array(797, 874),
   		'size_10x20' => array(614, 881, 7238),
   		'size_13x12' => array(361),
   		'size_15x10' => array(130, 131, 132, 133, 463, 563, 570),
   		//  
   		'size_15x10_no_resize' => array(488),
   		'size_15x15' => array(524, 569, 584, 657, 658, 722, 940),
   		'size_15x20_cnv' => array(483),
   		'size_20x15_cnv' => array(566, 798, 872, 2015),
   		'size_2035' => array(7381, 7382, 7383, 7384, 7385, 7386, 7397),
			



   		'size_20x20_cnv' => array(7357, 7358),
   		'size_20x25_cnv' => array(8, 276, 422, 484, 870),
   		'size_20x25_clock_square' => array(457),
   		'size_20x30_cnv' => array(184, 274, 360, 439, 485, 673, 876, 7230),
   		'size_30x10_cnv' => array(525),
   		'size_30x25_cnv' => array(895),
   		'size_30x25_clock_round' => array(172),
   		'size_25x30_cnv' => array(895),
   		'size_30x30_cnv' => array(653, 654, 813, 875),
   		'size_30x45_cnv' => array(7461),
   		'size_30x40_cnv' => array(275, 277, 359, 586, 800, 869),
   		'stabburet' => array(7196)
   		//'stamp' => array(7007),
   	)

   );

   Settings::Set( 'production', 'fotolab', array(
         '390_1' => 'LP-2507PsRGB',
         '390_2' => 'LP-2509PsRGB',
         '390_3' => 'sRGBFMPC',
         '570_1' => 'PICsRGB',
         'MyDevice' => 'MyDevice'
   
   ));
   
   
   Settings::Set( 'production' , 'sizename_border', array(
            '10cm' => array(
					419 => '10R13',
					1 => '102BC',
					14 => '10B10',
					6 =>'10R18',
					),
					'15cm' => array(
					2 => '15R20',
					1 => '15R10',
					1 => '15R10',
			 
			 
			  
            ),
            'both' => array(
					2 => '15R20',
					419 => '10R13',
					1 => '15R10',
					14 => '10R10',
					6 =>'10R18',
					7347 =>'15R20',
					1523 =>'15R20',
				
			 
			   
			   
			   
            ),
            'matt' => array(
					2 => '20R15',
					419 => '10R13',
					1 => '102BC',
					14 => '10R10',
					6 =>'10X18',
				
			  
			   
            )         
   ));
   
   Settings::Set( 'production', 'seperatorfile' , array(    
         '10X13' => 1,
         '102C' => 1,
         '15X10' => 1,
         '10X18' => 1
   ));
   
   
   Settings::set( 'production', 'calenderarray' , array( 184, 274, 359, 653, 654, 7357, 7358, ) );
   
   
   Settings::Set( 'production', 'sizename' , array(
			   //10cm paper
			   '10cm' => array(
					419 => '10X13',
					1   => '102C',
					14  => '10X10',
					6  => '10X18',
					614 =>'10X20',
					7238 =>'10X20',
					588 =>'10X15',
				
				 
			   ),
			   
			   //20cm paper
			   '20cm' => array(
			      2 => '20R15',
			   ),
			   
            //15cm paper
			   '15cm' => array(
					1  =>  '15X10',
					7345 => '15X15',
					2  =>  '15X20',
					7347 => '15X20',
					1523 =>'15R20',
					7442 => '15X15',
					

			

				 
			   ),
			   //10cm and 15cm
			   
			   'both' => array(
					2 => '15X20',
					1 => '15X10',
					419 => '10X13',
					14  => '10X10',
					6  => '10X18',
					7345 => '15X15',
					7238 =>'10X20',
					7442 => '15X15',
			   

			 
			),
			'matt' => array(
			    1 => '102C',
			    2 => '20X15',
			    2015 =>'20X15',
				1318 =>'20X15',
			    419 => '10X13',
			    14  => '10X10',
			    7347 => '15X20',
				 1523 =>'15R20',
				  
			    6  => '10X18',
			    7238 =>'10X20',
			    7345 => '15X15',
			    7442 => '15X15',
				

				      
			),
			   7442 => '15X15',
			   483 => '15X20',
			   1318 => '15X20',
			   524 => '15X15',
			   569 => '15X15',
			   584 => '15X15',
			   658 => '15X15',
			   722 => '15X15',
			   940 => '15X15',
			   657 => '15X15',
			   560 => '10X18',
			   693 => '10X9' ,
				7551 => '10X9' ,
			   7254 => '10X9',
				7298  => '10X18',
			   6  => '10X18',
			   7  =>  '10X18',
			   9 =>   '10X18',
			   129 => '10X18',
			   522 => '102C',
			   558 => '10X18',
			   559 => '10X18',
			   560 => '10X18',
			   561 => '10X18',
			   564 => '10X18',
			   571 => '10X18',
			   939 => '10X18',
			   873 => '10X18',
			   6038 =>'10X18',
			   604 => '10X20',
			   881 => '10X20',
			   557 => '10X9',
			   675 => '10X9',
			   
			   
			   //15cm paper
			   
			   130 => '15X10',
			   131 => '15X10',
			   132 => '15X10',
			   133 => '15X10',
			   463 => '15X10',
			   563 => '15X10',
			   570 => '15X10',
			   
			   

			   //20cm paper
			   7381 => '2035',
			   7382 => '2035',
			   7383 => '2035',
			   7384 => '2035',
			   7385 => '2035',
			   7386 => '2035',
			   7397 => '2035',
			   7383 => '2035',
				439 => '20X30',
				3   => '20X25',
				422 => '20X25',
				8   => '20X25',
				276 => '20X25',
				484 => '20X25',
				870 => '20X25',
				425 => '20X25',
				566 => '20X15',
				798 => '20X15',
				1318 => '20X15',
				2015 => '20X15',
				872 => '20X15',
				7347 =>'20X15',
				1523 =>'15R20',
				184 => '20X30',
				274 => '20X30',
				360 => '20X30',
				485 => '20X30',
				673 => '20X30',
				876 => '20X30',
				7230 => '20X30',
				7357 => '20X20',
				7358 => '20X20',
			   
			   //30cm paper
			   525 => '30X10',
			   653 => '30X30',
			   654 => '30X30',
			   813 => '30X30',
			   875 => '30X30',
			   275 => '30X40',
			   277 => '30X40',
			   359 => '30X40',
			   586 => '30X40',
			   800 => '30X40',
			   869 => '30X40',
			   4   => '30X25',
			   5   => '30X40',
			   895 => '30X25',
			   7398 =>'30X45',
			   7461 =>'30X45',
			   
			   
			   
		
			   
   ));
   
   

?>