<?php//
    //
   // by ~cy
  //
 //
	function is_ani($filename) {
	    if(!($fh = @fopen($filename, 'rb')))
	        return false;
	    $count = 0;
	    //an animated gif contains multiple "frames", with each frame having a
	    //header made up of:
	    // * a static 4-byte sequence (\x00\x21\xF9\x04)
	    // * 4 variable bytes
	    // * a static 2-byte sequence (\x00\x2C)

	    // We read through the file til we reach the end of the file, or we've found
	    // at least 2 frame headers
	    while(!feof($fh) && $count < 2) {
	        $chunk = fread($fh, 1024 * 100); //read 100kb at a time
	        $count += preg_match_all('#\x00\x21\xF9\x04.{4}\x00\x2C#s', $chunk, $matches);
	    }
	    fclose($fh);
	    return $count;
	}
    if ( isset($_POST["url"]) ) {
        if (is_ani($_POST["url"]) > 1){
            echo "1";
        }else{
            echo "2";
        }
        die;
    }
    if ( isset($_GET["url"]) ) {
        if (is_ani($_GET["url"]) > 1){
            echo "1";
        }else{
            echo "2";
        }
        die;
    }?>


<html>

<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <title>Imgur Random ANIMATED</title>
    <script src="//code.jquery.com/jquery-1.10.2.js"></script>
    <script type='text/javascript' src='https://code.jquery.com/jquery-1.5.2.js'></script>
    <style type='text/css'>
        body {
            background-color: #000;
            color: #fff;
        }
        h1 {
            margin-bottom: 20px;
        }
        #images {
            margin: 20px 0;
        }
        #images img {
            margin-right: 5px;
            border: 2px solid #000;
        }
        #images img:hover {
            border: 2px solid #fff;
        }
    </style>
    <script type='text/javascript'>
        //<![CDATA[ 
        imgurcache = new Array()
        $(window).load(function() {
            var Imgur = {
                fetch: function(num) {
                    var self = this;
                    self.total = num;
                    self.done = 0;
                    self.failures = 0;
                    self.start = +new Date;

                    $('#images').empty();
                    for (var x = 0; x < num; x++) {
                        self.hunt(function(id) {
                            self.done++;
                            $('#images').append("<a href='https://i.imgur.com/" + id + ".webm' target='_blank' rel='noreferrer'><img src='https://i.imgur.com/" + id + ".gif' height='110' width='110' /></a>");
                            self.update();
                        });
                    }
                },
                update: function() {
                    var interval = new Date - this.start;

                    function speed(v) {
                        return (~~(v / interval * 1e5)) / 100;
                    }
                    $('#info').html((this.done < this.total ? "Loading.. " + this.done + "/" + this.total + " (" + this.failures + " failures" + ") " : "Done. ") + "[" + speed(this.failures + this.done) + " req/s - " + speed(this.done) + " img/s]");
                },

                hunt: function(cb) {
                    var self = this,
                        id = self.random(5),
                        img = new Image;
                    self.update();
                    img.src = "https://i.imgur.com/" + id + "s.jpg";
                    img.onload = function() {
                        if ((img.width == 198 && img.height == 160) || (img.width == 161 && img.height == 81)) {
                            // assume this is an imgur error image, and retry.
                            fail();
                        } else {
                        	$.post( "fskr.php", { url:"https://i.imgur.com/" + id + ".gif" })
							.done(function( data ) {
								if (data == 1){
                        			cb(id);
								}else{
									fail();
								}
							});
                        }
                    }
                    img.onerror = fail; // no escape.
                    function fail() {
                        self.failures++;
                        self.update();
                        self.hunt(cb);
                    }
                },
                random: function(len) {
                    var text = new Array();
                    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
                    for (var i = 0; i < len; i++) {
                        imgurchar = possible.charAt(Math.floor(Math.random() * possible.length));
                        if (text.indexOf(imgurchar) == -1) {
                            text.push(imgurchar);
                        } else {
                            i--;
                        }
                    }
                    text = text.join('');
                    if (imgurcache.indexOf(text) == -1) {
                        imgurcache.push(text);
                        return text;
                    } else {
                        self.random(5);
                        return false;
                    }
                }
            };

            $('#random').bind('click', function(e) {
                Imgur.fetch($("#number").val());
            });
        }); //]]>
    </script>
</head>

<body>

    <p id="info"></p>
    <div id="images"></div>


    </br>
    <center>
        <center>
            <p>
                Number of imags to display before script ends. <br>
                <input type="text" id="number" value="5"> more than 10 can take a long time<br>
                <button id="random">Show me random imgur ANIMATED pics!</button>
            </p>
</body>

</html>