<?php
return array(

    'GrindStone'=>array(
    
        // xml file settings
        'xmlFiles'=>array(
            // one array element per staff and computer
            'John'=>array(
                'file'=>'C:/Documents and Settings/John/AppData/Grindstone 2/config.gsc2',
                'staff'=>'John Smith',
                'computer'=>'JOHN-PC',
            ),
        ),
		
		// get the period groups
        'getPeriods'=>true,

        // hide times before this date (blank to show all)
        'startDate'=>'-20days',

        // save a file for each day
        'days'=>20,

    ),

    'Redmine' => array(

        // url and key
        'url' => 'http://redmine.example.local/',
        'archivePath' => 'C:/Documents and Settings/John/Timesheets/__redmine/uploaded_grindstone_time/',
        'users' => array(
            'John' => 'redmine_api_key',
        ),

        // profile to issue_id mapping
        'profiles' => array(
            // profile => issue_id
            'XYZ Pty Ltd' => 123456,
        ),

    ),

    'Saasu' => array(
        'staff' => array(
            'John' => array(
                'cost' => 110, // hourly cost inc
                'rate' => 220, // hourly rate inc
                'multiplier' => 1, // multiple billable hours by this
                'profileRates' => array(
                    // profile => rate inc gst
                    'XYZ Pty Ltd' => 165,
                ),
                'profileMultiplier' => array(
                    // profile => hours multiplier
                    'XYZ Pty Ltd' => 0.8,
                ),
            ),
        ),
        'url' => 'https://secure.saasu.com/webservices/rest/r1/',
        'token' => 'saasu_api_key',
        'fileId' => 1234,
        'taxCode' => 'G1', // either gst: 'G1', or exgst:'G1,G2'
        'layout' => 'I', // S=service I=item
        'taxAccount' => 76840, // (required for layout=S) see saasu > view > accounts
        'inventoryItemUid' => 56982, // (required for layout=I) see saasu > items
        'contactId' => 221813,
        'sendEmail' => true,
        'fromEmail' => 'sales@example.local',
        'emailTemplate' => 'invoice', // see views/email/invoice.*.php
        'profiles' => array(
            'XYZ Pty Ltd' => array(
                'contactId' => 221813,
                'email' => 'sales@xyz.local',
            ),
        ),
    ),
	
);