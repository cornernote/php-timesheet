<?php
return array(

    'GrindStone'=>array(
    
        // xml file settings
        'xmlFiles'=>array(
            // one array element per staff and computer
            'Zain'=>array(
                'file'=>'C:/Documents and Settings/Zain/AppData/Grindstone 2/config.gsc2',
                'staff'=>'Zain ul abidin',
                'computer'=>'ZAIN-PC',
            ),
        ),

        // hide times before this date (blank to show all)
        'startDate'=>'-6days',

        // save a file for each day
        'getPeriods'=>true,
        'days'=>7,

    ),

    'ActiveCollab'=>array(
    
        // url and token for AC
        'url'=>'http://my.mrphp.com.au/api.php',
        'token'=>'00-ABC123...789XYZ',

        // all uploaders need to have the same dropbox path
        'archivePath'=>'C:/Users/PC/Documents/My Dropbox/mrphp/Timesheets/__activecollab/uploaded_grindstone_time/',
        
        // profile to project_id mapping
        'profiles'=>array(
            // profile => project_id
            'Mr PHP'=>2,
            'AFI Branding'=>1,
            'Factory Fast'=>15,
            'The Reading Room'=>3,
            'CarbaTec'=>6,
        ),
        'users'=>array(
            // one user per xml file to map the GS file to an AC user
            'Zain'=>25,
        ),
        
    ),

    'Redmine'=>array(
    
        // url and key
        'url'=>'http://service.mrphp.com.au/',
        'key'=>'your_api_key',
        'archivePath'=>'F:/documents/Brett/Dropbox/mrphp/Timesheets/__redmine/uploaded_grindstone_time/',
        'user'=>'brett',
        
        // profile to issue_id mapping
        'profiles'=>array(
            // profile => issue_id
            'Mr PHP'=>10087,
            'AFI Branding'=>10058,
            'The Look Company'=>10060,
            'Factory Fast'=>10088,
            'CarbaTec'=>10073,
        ),
        
    ),

    'Saasu'=>array(
    
        // rates for staff and clients
        'staff'=>array(
            'Zain'=>array(
                'rate'=>30,
                'profileRates'=>array(
                    // profile => rate inc gst
                    'Mr PHP'=>30,
                    'AFI Branding'=>30,
                    'Factory Fast'=>30,
                    'The Reading Room'=>30,
                    'CarbaTec'=>30,
                ),
            ),
        ),
        
        // saasu url and token
        'url'=>'https://secure.saasu.com/webservices/rest/r1/',
        'token'=>'ABC123...789XYZ',
        'fileId'=>12345,
        
        // either gst: 'G1', or exgst:'G1,G2'
        'taxCode'=>'G1',
        
        // S=service I=item -- note: Service is not fully tested 
        'layout'=>'I', 
        
        // (required for layout=S) see saasu > view > accounts
        'taxAccount'=>12345,
        
        // (required for layout=I) see saasu > items
        'inventoryItemUid'=>12345,
        
        // the contact to invoice if a contact cannot be found 
        'contactId'=>12345,
        
        // email details
        'sendEmail'=>true,
        'fromEmail'=>'you@example.com',
        'emailTemplate'=>'invoice',

        // profile contact and email info
        'profiles'=>array(
            'Mr PHP'=>array(
                'contactId'=>12345,
                'email'=>'you@example.com',
            ),
            'AFI Branding'=>array(
                'contactId'=>12345,
                'email'=>'you@example.com',
            ),
            'Factory Fast'=>array(
                'contactId'=>12345,
                'email'=>'you@example.com',
            ),
            'The Reading Room'=>array(
                'contactId'=>12345,
                'email'=>'you@example.com',
            ),
            'CarbaTec'=>array(
                'contactId'=>12345,
                'email'=>'you@example.com',
            ),
        ),
    ),
);