<?php
return array(

    'GrindStone'=>array(
    
        // xml file settings
        'xmlFiles'=>array(
            // one array element per staff and computer
            'Guy'=>array(
                'file'=>'C:/Documents and Settings/Guy/AppData/Grindstone 2/config.gsc2',
                'staff'=>'Guy ul abidin',
                'computer'=>'GUY-PC',
            ),
        ),

        // hide times before this date (blank to show all)
        'startDate'=>'-6days',

        // save a file for each day
        'getPeriods'=>true,
        'days'=>7,

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
                'baseHours'=>'3',
                'baseRate'=>'300',
            ),
        ),
    ),
);