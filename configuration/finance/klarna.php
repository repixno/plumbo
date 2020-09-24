<?
library( 'Klarna.Invoice.Klarna' );



if( Dispatcher::getPortal() == 'UP-001' ){
    
    Settings::Set( 'finance', 'testklarna', array(
            'eid'           => '11481',
            'sharedSecret'  => 'pngsrzpxUmKAxHU',
            'server'        => 'checkout.testdrive.klarna.com',
        ));
    
    Settings::Set( 'finance', 'klarna', array(
            'eid'           => '33079',
            'sharedSecret'  => 'mrNChGiUnBObM6E',
            'server'        => 'checkout.klarna.com',
        ));
    
        
    Settings::Set( 'finance', 'testklarnainvoice', array(
            'eid'           => '37617',
            'sharedSecret'  => 'rYIDaRZ3Kvhuj7F',
            'server'        => Klarna::LIVE,
            //'eid'           => '2990',
            //'sharedSecret'  => 'gqXt7xuBYZUdvrP',
            //'server'        => Klarna::BETA,
        ));
    
    Settings::Set( 'finance', 'klarnainvoice', array(
            'eid'           => '37617',
            'sharedSecret'  => 'rYIDaRZ3Kvhuj7F',
            'server'        => Klarna::LIVE,
        ));
    
    
    
}



// Fotono
elseif( Dispatcher::getPortal() == 'FOTONO' ){
    
   Settings::Set( 'finance', 'testklarna', array(
    //'eid'           => '2990',
        //'sharedSecret'  => 'gqXt7xuBYZUdvrP',
        //'server'        => 'checkout.testdrive.klarna.com',
        'eid'           => '60581',
        'sharedSecret'  => 'FGvFZPIRn7ZFyqK',
        'server'        => 'checkout.klarna.com',
    ));

Settings::Set( 'finance', 'klarna', array(
        'eid'           => '113925',
        'sharedSecret'  => 'qgTvZYFCeyhYej8',
        'server'        => 'checkout.klarna.com',
    ));

    
Settings::Set( 'finance', 'testklarnainvoice', array(
        'eid'           => '37617',
        'sharedSecret'  => 'rYIDaRZ3Kvhuj7F',
        'server'        => Klarna::LIVE,
        //'eid'           => '2990',
        //'sharedSecret'  => 'gqXt7xuBYZUdvrP',
        //'server'        => Klarna::BETA,
    ));

Settings::Set( 'finance', 'klarnainvoice', array(
        'eid'           => '37617',
        'sharedSecret'  => 'rYIDaRZ3Kvhuj7F',
        'server'        => Klarna::LIVE,
    ));
    
}


//eurofoto

elseif( Dispatcher::getPortal() == '' ){
    
  
Settings::Set( 'finance', 'klarna', array(
        'eid'           => '113834',
        'sharedSecret'  => 'MOSzrXmEHAwvPWe',
        'server'        => 'checkout.klarna.com',
    ));
    
}

//dinmerkelapp

else{

Settings::Set( 'finance', 'testklarna', array(
        //'eid'           => '2990',
        //'sharedSecret'  => 'gqXt7xuBYZUdvrP',
        //'server'        => 'checkout.testdrive.klarna.com',
        'eid'           => '33211',
        'sharedSecret'  => 'GDSzTXfQQcWghqd',
        'server'        => 'checkout.klarna.com',
    ));

Settings::Set( 'finance', 'klarna', array(
        'eid'           => '113834',
        'sharedSecret'  => 'MOSzrXmEHAwvPWe',
        'server'        => 'checkout.klarna.com',
    ));

    
Settings::Set( 'finance', 'testklarnainvoice', array(
        'eid'           => '37617',
        'sharedSecret'  => 'rYIDaRZ3Kvhuj7F',
        'server'        => Klarna::LIVE,
        //'eid'           => '2990',
        //'sharedSecret'  => 'gqXt7xuBYZUdvrP',
        //'server'        => Klarna::BETA,
    ));

Settings::Set( 'finance', 'klarnainvoice', array(
        'eid'           => '37617',
        'sharedSecret'  => 'rYIDaRZ3Kvhuj7F',
        'server'        => Klarna::LIVE,
    ));

}    
    
?>
