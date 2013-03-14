<?php
/**
 * WARNING: This only works with version >= 1.0 of the AmazonECS library
 */

/**
 * For a running Search Demo see: http://amazonecs.pixel-web.org
 */

if ("cli" !== PHP_SAPI)
{
    echo "<pre>";
}

if (is_file('sampleSettings.php'))
{
  include 'sampleSettings.php';
}

defined('AWS_API_KEY') or define('AWS_API_KEY', 'API KEY');
defined('AWS_API_SECRET_KEY') or define('AWS_API_SECRET_KEY', 'SECRET KEY');
defined('AWS_ASSOCIATE_TAG') or define('AWS_ASSOCIATE_TAG', 'ASSOCIATE TAG');
defined('AWS_ANOTHER_ASSOCIATE_TAG') or define('AWS_ANOTHER_ASSOCIATE_TAG', 'ANOTHER ASSOCIATE TAG');

require '../lib/AmazonECS.class.php';

try
{
    // get a new object with your API Key and secret key.

    // Added in version 1.0 is the new optional parameter to set up an AssociateTag (AssociateID)
    $amazonEcs = new AmazonECS(AWS_API_KEY, AWS_API_SECRET_KEY, 'COM', AWS_ASSOCIATE_TAG);

    /**
     * Now you can work the normal way with the class with the difference
     * that every URL in the response contains your AssociateTag
     */
    $r = $amazonEcs->returnType(AmazonECS::RETURN_TYPE_ARRAY)->responseGroup('BrowseNodeInfo')->browseNodeLookup('565108');
    print_r($r);exit;
    //search product by category and node
    $response = $amazonEcs->category('Electronics')->responseGroup('Small,Images')->search("", 565108);
    print_r($response);

    // searching again
    //$response = $amazonEcs->search('Bud Spencer');
    //var_dump($response);

    // Use the new Setter to update your AssociateTag on the fly
    //$response = $amazonEcs->associateTag(AWS_ANOTHER_ASSOCIATE_TAG)->search('Bud Spencer');
    //var_dump($response);


    // For more examples please look at testItemSearch.php and testItemLookup.php
    // These examples also could be used with the AssociateTag
}
catch(Exception $e)
{
  echo $e->getMessage();
}

if ("cli" !== PHP_SAPI)
{
    echo "</pre>";
}
