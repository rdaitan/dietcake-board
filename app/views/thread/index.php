<div class="row">
    <div class="col-md-8">
        <div>
            <h1>All Threads</h1>
            <?php if($authUser) {?>
                <h3>Welcome, <?php eh($authUser->username); ?>!</h3>
            <?php } ?>
        </div>
        <div id="thread_list">
            <?php foreach($threads as $thread) { ?>
                <div class='thread'>
                    <div>
                        <a href="<?php eh(url('thread/view', array('thread_id' => $thread->id))) ?>">
                            <?php eh($thread->title); ?>
                        </a>
                    </div>
                </div>
            <?php } ?>
            <a href="<?php eh(url('thread/create')); ?>" class="btn btn-large btn-primary">Create</a>
        </div>
    </div>
    <div class="col-md-4"></div>
</div>

<!--Pagination-->
<?php printPageLinks($pagination, $pages); ?>

<!--Login/Logout/Signup-->
<?php if(User::getAuthUser()) { ?>
    <a href="<?php eh(url('user/logout')); ?>" class="btn btn-danger">Log out</a>
<?php } else { ?>
    <a href="<?php eh(url('user/create')); ?>" class="btn btn-primary">Sign Up</a>
    <a href="<?php eh(url('user/authenticate')); ?>" class="btn btn-success">Log in</a>
<?php } ?>
