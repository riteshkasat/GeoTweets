<!DOCTYPE html>
<html>
<head>
    <title>GeoTweets 1234567890</title>
    <meta charset="utf-8">
</head>
<body>

<h1>GeoTweets</h1>
<form method = "post" action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    Domain:<select name= "domains">
        <option value = "History">History</option>
        <option value = "Culture">Culture</option>
        <option value = "Sport">Sport</option>
    </select>

    <input type="submit" name="submit" value="Submit">
</form>

<?php

// connected to mongodb
$m = new MongoClient();

// select a database
$db = $m->test;

// select a collection
$collection = $db->Domain;

// 查询文档，确定后台已计算出关键词
$amount = $collection->count(array('domain' => 'Culture'));

if($amount == 0)//if you have not yet calculated, then call the script
{
    $status = exec('python E:\PythonProject\Twitter\Test\GetKeywords.py');
    if($status == 'finished')
    {
        exec('python E:\PythonProject\Twitter\Test\AcquireTwitters.py');
    }
}
else//Keywords background has been calculated
{

}

$domain = "";

if($_SERVER['REQUEST_METHOD'] == 'POST')//If you submitted a request
{
    $domain = $_POST['domains'];

    $arr = array('type' => 'FeatureCollection', 'id' => 'tweetsyoulike.c22ab257',
        'features' => array());

    $collection = $db->Twitter;

    $cursor = $collection->find();
    $i = 1;
    while($cursor->hasNext())
    {
        $document = $cursor->getNext();

        $arr['features'][] = array('type' => 'Feature', 'id' => $document['_id'],
            'geometry' => array('coordinates' => $document['coordinates'], 'type' => 'Point'),
            'properties' => array('id' => $document['_id'], 'time' => $document['created_at'],
                'name' => $document['name'], 'text' => $document['text'], 'location' => $document['location'],
                'media' => $document['media_url'], 'marker-size' => 'medium',
                'marker-color' => '#7ec9b1', 'marker-symbol' => '3',
                'importance' => $document['importance']));
    }

    $jsonData = json_encode($arr);//Converted into json data
    echo $jsonData;

    /*echo '{"type":"FeatureCollection","id":"tweetsyoulike.c22ab257","features":[';

    $collection = $db->Twitter;
    $cursor = $collection->find();
    $i = 1;
    while($cursor->hasNext())
    {
        $document = $cursor->getNext();
        $arr = array('type' => 'Feature', 'id' => $document['_id'],
            'geometry' => array('coordinates' => $document['coordinates'], 'type' => 'Point'),
            'properties' => array('id' => $document['_id'], 'time' => $document['created_at'],
                'name' => $document['name'], 'text' => $document['text'], 'location' => $document['location'],
                'media' => $document['media_url'], 'marker-size' => 'medium',
                'marker-color' => '#7ec9b1', 'marker-symbol' => '3',
                'importance' => $document['importance']));
        $jsonData = json_encode($arr);//转换成json数据
        echo $jsonData;
        if($cursor->hasNext())
            echo ',';
    }
    echo ']}';*/

    // Search databases, check whether the twitter of importance included domain, if so, the result in the returned array , can be used to display sorted
    // If not, call the python script to get twitter, inserted into the database . Get all twitter from the database , calculates the domain of the keywords of importance, after the completion of return to php
    // ok, then php then check from the database.
    exec('python E:\PythonProject\Twitter\Test\TextMining.py Culture');//执行，不输出返回值，保留结果
    //system('python E:\PythonProject\Twitter\Test\TextMining.py Culture');//执行，输出返回值，保留结果

}

?>

</body>
</html>
