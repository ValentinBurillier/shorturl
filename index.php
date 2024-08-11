<?php

  // IS RECEIVED SHORTCUT
  if(isset($_GET['q'])) {
    // VARIABLE
    $shortcut = htmlspecialchars($_GET['q']);

    // IS A SHORTCUT ?
    $bdd = new PDO('mysql:host=localhost;dbname=shorturl;charset=utf8', 'root', '');
    $req = $bdd->prepare('SELECT COUNT(*) AS x FROM links WHERE shorturl = ?');
    $req->execute(array($shortcut));

    while($result = $req->fetch()) {
      if($result['x'] != 1) {
        header('location:/?error=true&message=adresse url non connue');
        exit();
      }
    }
    // REDIRECTION
  $req = $bdd->prepare('SELECT * FROM links WHERE shorturl = ?');
  $req->execute(array($shortcut));

  while($result = $req->fetch()) {
    header('location: '.$result['url']);
    exit();
  }
  }

  

  // SENDING FORM
  if(isset($_POST['url'])) {

    // VARIABLE
    $url = $_POST['url'];

    // VERIFICATION
    if(!filter_var($url, FILTER_VALIDATE_URL)) {
      // PAS UN LIEN => REDIRECTION
      header('location:/?error=true&message=adresse url non valide');
      exit(); // EVITE DE CONTINUER LE CODE. RECOMMANDER APRES CHAQUE HEADER
    }
    // SHORTCUT
    $shortcut = crypt($url, time());

    // HAS BEEN ALREADY SEND ?
    $bdd = new PDO('mysql:host=localhost;dbname=shorturl;charset=utf8', 'root', '');
    $req = $bdd->prepare('SELECT COUNT(*) AS x FROM links WHERE url = ?');
    $req->execute(array($url));

    while($result = $req->fetch()) {
      if($result['x'] != 0) {
        header('location:/?error=true&message=adresse déjà raccourcie');
        exit();
      }
    }
    // SENDING
    $req = $bdd-> prepare('INSERT INTO links(url, shortcut) VALUES (?, ?)');
    $req->execute(array($url, $shortcut));
    header('location: ../?short=' . $shortcut);
    exit();
  }
?>

<!DOCTYPE html>
<html lang="fr-FR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ShortUrl - Raccourciceur d'url</title>
  <link href="style.css" rel="stylesheet" type="text/css">
</head>
<body>
  <form method="post">
    <input type="url" name="url" placeholder="Entrez votre url">
    <button type="submit">Raccoucir</button>
  </form>

  <?php 
    if(isset($_GET['error']) && isset($_GET['message'])) { ?>
      <b><?php echo htmlspecialchars($_GET['message']);?></b>
    <?php } else if (isset($_GET['short'])) {
      ?>
        <b>URL RACCOURCIE : </b>
        http://localhost/?q=<?php echo htmlspecialchars(($_GET['short'])); ?>
    <?php }?>
</body>
</html>