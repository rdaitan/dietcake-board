<?php
class Comment extends AppModel
{
    const MIN_BODY_LENGTH   = 1;
    const MAX_BODY_LENGTH   = 200;
    const TABLE_NAME        = 'comment';

    public $validation = array(
        'body' => array(
            'length' => array('validate_between', self::MIN_BODY_LENGTH, self::MAX_BODY_LENGTH)
        ),
    );

    public function __construct(array $data = array())
    {
        parent::__construct($data);

        if (!isset($this->user_id)) {
            return;
        }

        $user = User::getById($this->user_id);
        $this->username = $user->username;
    }

    public static function countAll($thread_id)
    {
        $db = DB::conn();
        return $db->value("SELECT COUNT(*) FROM comment WHERE thread_id=?", array($thread_id));
    }

    public static function getAll($thread_id, $offset, $limit)
    {


        $db     = DB::conn();
        $rows   = $db->rows(
            sprintf("SELECT * FROM comment WHERE thread_id=? LIMIT %d, %d", $offset, $limit),
            array($thread_id)
        );

        $comments = array();

        foreach ($rows as $row) {
            $comments[] = new self($row);
        }

        return $comments;
    }

    public static function get($id)
    {
        $db     = DB::conn();
        $row    = $db->row("SELECT * FROM comment WHERE id=?", array($id));

        return $row ? new self($row) : false;
    }

    public static function getOrFail($id)
    {
        $comment = self::get($id);

        if($comment) {
            return $comment;
        } else {
            throw new RecordNotFoundException();
        }
    }

    public static function getFirstInThread(Thread $thread)
    {
        $db = DB::conn();
        $row = $db->row(sprintf('SELECT * FROM %s WHERE thread_id=?', self::TABLE_NAME), array($thread->id));

        return $row ? new self($row) : false;
    }

    public static function getTrendingThreadIds($limit)
    {
        $db = DB::conn();
        return  $db->rows(
            sprintf(
                'SELECT thread_id, COUNT(*) AS count FROM %s
                    WHERE DATE(created_at)=DATE(CURRENT_TIMESTAMP) GROUP BY
                    thread_id ORDER BY count DESC, created_at DESC LIMIT 0, %d',
                self::TABLE_NAME,
                $limit
            )
        );
    }

    public function create(Thread $thread)
    {
        if (!$this->validate()) {
            throw new ValidationException('Invalid comment.');
        }

        $db = DB::conn();
        $db->insert(
            'comment',
            array(
                'thread_id' => $thread->id,
                'user_id'   => $this->user_id,
                'body'      => $this->body,
                'created_at'=> null
            )
        );
    }

    public function update()
    {
        if (!$this->validate()) {
            throw new ValidationException('Invalid comment.');
        }

        $db = DB::conn();
        $db->update(
            'comment',
            array('body' => $this->body),
            array('id' => $this->id)
        );
    }

    public function delete() {
        $db = DB::conn();
        $db->query('DELETE FROM comment WHERE id=?', array($this->id));
    }

    public function isOwnedBy($user)
    {
        if (!$user) {
            return false;
        }

        return $user->id == $this->user_id;
    }
}
