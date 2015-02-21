<?php

define('EMBED_PAGE', true);

include 'global.php';

if( empty($_GET['token']) || isset($_GET['token']) && $_GET['token'] !== VALIDATING_HASH ) {
  exit;
}

function fake_repo() {
  $repo = new stdClass;  
  $repo->owner = new stdClass;
  $repo->owner->login = '';
  $repo->name = '';
  
  return $repo;
}

function get_repo() {
  if( isset($_GET['owner']) && isset($_GET['repo']) ) {
    $repo = GitHub_Repo::find($_GET['owner'], $_GET['repo']);
    
    if( isset($repo->message) && $repo->message == 'Not Found' ) {
      $repo = fake_repo();
    }
  }else {
    $repo = fake_repo();
  }
  
  return $repo;
}

if( isset($_POST['issue']) ) {  
  $owner = isset($_POST['owner']) ? $_POST['owner'] : null;
  $repo_name = isset($_POST['repo']) ? $_POST['repo'] : null;
  
  $issue_title = isset($_POST['issue']['title']) ? $_POST['issue']['title'] : null;
  $issue_body = isset($_POST['issue']['body']) ? $_POST['issue']['body'] : null;
  
  if( isset($owner, $repo_name, $issue_title, $issue_body) ) {
    $issue = GitHub_Client::post(sprintf('/repos/%s/%s/issues', $owner, $repo_name), array(
      'title' => $issue_title,
      'body' => $issue_body,
      'labels' => array('from-client')
    ));
        
    if( isset($issue->errors) || isset($issue->message) && $issue->message = 'Not Found' ) {
      $errors = true;
      $repo = get_repo();
    }else {
      $submitted = true;
    }
  }
}else {
  $repo = get_repo();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Submit a new Ticket</title>
<meta charset="utf-8" />

<link rel="stylesheet" href="css/bootstrap.min.css" />
<link rel="stylesheet" href="css/bootstrap-responsive.min.css" />

<style>

  section {
    padding: 0 20px;
  }

  input, textarea {
    width: 98%;
  }
  
  textarea {
    height: 300px;
    resize: none;
  }
</style>
<link rel="stylesheet" href="css/embed.css" />
</head>
<body>
  
  <header>
    <h2>Submit a new Ticket</h2>
  </header>
  
  <section>
    
    <?php if( isset($submitted) ) : ?>
      <p class="alert alert-success">Your Ticket was Submitted Successfully!</p>
    <?php else : ?>
      
      <?php if( isset($errors) ) : ?>
        <p class="alert alert-error">There was an error submitting the ticket</p>
      <?php endif ?>
      <form method="POST">
        <input type="hidden" name="owner" value="<?php echo $repo->owner->login ?>" />
        <input type="hidden" name="repo" value="<?php echo $repo->name ?>" />

        <label for="issue_title">Ticket Title</label>
        <input autocomplete="off" type="text" id="issue_title" name="issue[title]" />

        <label for="issue_description">Description</label>
        <textarea id="issue_description" name="issue[body]"></textarea>

        <div class="form-actions">
          <button class="btn btn-primary">Submit Ticket</button>
        </div>
      </form>
    <?php endif ?>
  </section>
  
</body>
</html>