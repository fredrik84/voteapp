   <!-- Main Content -->
<?php
   if(!empty($this->query[0])) {
      foreach($this->query as $value) {
?>
      <section>
         <header>
            <div name="news_id<?=$id?>"></div>
            <h2><?=$value->title?></h2>
            <h3><?=$value->subtitle?></h3>
         </header>
         <p>
         <?=$value->body?>
         </p>
      </section>
<?php
      }
   }
?>
</div>
<div class="3u">

   <!-- Sidebar -->
      <section>
         <header>
            <h2>Scene links</h2>
         </header>
         <ul class="link-list">
            <li><a target="_new" href="http://www.pouet.net">pouet.net</a></li>
            <li><a target="_new" href="http://www.scene.org">scene.org</a></li>
            <li><a target="_new" href="http://www.demoparty.net">demoparty.net</a></li>
         </ul>
      </section>
      <section>
         <header>
            <h2>Previous news</h2>
         </header>
         <ul class="link-list">
<?php
   foreach($short_list as $id => $title) {
?>
            <li><a href="#news_id<?=$id?>"><?=$title?></a></li>
<?php
   }
?>
         </ul>
      </section>


