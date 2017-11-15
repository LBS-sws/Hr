<?php
class CReport {
	public $criteria;
	
	public $data = array();
	
	public function genReport() {
		return;
	}

	protected function sendEmail(&$connection, $record=array()) {
		$suffix = Yii::app()->params['envSuffix'];
		$suffix1 = ($suffix=='dev') ? '_w' : $suffix;
		$sql = "insert into swoper$suffix1.swo_email_queue
					(from_addr, to_addr, cc_addr, subject, description, message, status, lcu)
				values
					(:from_addr, :to_addr, :cc_addr, :subject, :description, :message, 'P', 'admin')
			";
		$command = $connection->createCommand($sql);
		if (strpos($sql,':from_addr')!==false)
			$command->bindParam(':from_addr',$record['from_addr'],PDO::PARAM_STR);
		if (strpos($sql,':to_addr')!==false)
			$command->bindParam(':to_addr',$record['to_addr'],PDO::PARAM_STR);
		if (strpos($sql,':cc_addr')!==false)
			$command->bindParam(':cc_addr',$record['cc_addr'],PDO::PARAM_STR);
		if (strpos($sql,':subject')!==false)
			$command->bindParam(':subject',$record['subject'],PDO::PARAM_STR);
		if (strpos($sql,':description')!==false)
			$command->bindParam(':description',$record['description'],PDO::PARAM_STR);
		if (strpos($sql,':message')!==false)
			$command->bindParam(':message',$record['message'],PDO::PARAM_STR);
		$command->execute();
	}
	
	protected function sendEmailWithAttachment(&$connection, $record=array(), $attachment=array()) {
		$suffix = Yii::app()->params['envSuffix'];
		$suffix1 = ($suffix=='dev') ? '_w' : $suffix;

		$transaction=$connection->beginTransaction();
		try {
			$sql = "insert into swoper$suffix1.swo_email_queue
						(from_addr, to_addr, cc_addr, subject, description, message, status, lcu)
					values
						(:from_addr, :to_addr, :cc_addr, :subject, :description, :message, 'P', 'admin')
				";
			$command = $connection->createCommand($sql);
			if (strpos($sql,':from_addr')!==false)
				$command->bindParam(':from_addr',$record['from_addr'],PDO::PARAM_STR);
			if (strpos($sql,':to_addr')!==false)
				$command->bindParam(':to_addr',$record['to_addr'],PDO::PARAM_STR);
			if (strpos($sql,':cc_addr')!==false)
				$command->bindParam(':cc_addr',$record['cc_addr'],PDO::PARAM_STR);
			if (strpos($sql,':subject')!==false)
				$command->bindParam(':subject',$record['subject'],PDO::PARAM_STR);
			if (strpos($sql,':description')!==false)
				$command->bindParam(':description',$record['description'],PDO::PARAM_STR);
			if (strpos($sql,':message')!==false)
				$command->bindParam(':message',$record['message'],PDO::PARAM_STR);
			$command->execute();

			if (!empty($attachment)) {
				$id = $connection->getLastInsertID();
				$sql = "insert into swoper$suffix1.swo_email_queue_attm
							(queue_id, name, content)
						values
							(:queue_id, :name, :content)
					";
				foreach ($attachment as $key=>$content) {
					$command = $connection->createCommand($sql);
					if (strpos($sql,':queue_id')!==false)
						$command->bindParam(':queue_id',$id,PDO::PARAM_INT);
					if (strpos($sql,':name')!==false)
						$command->bindParam(':name',$key,PDO::PARAM_STR);
					if (strpos($sql,':content')!==false)
						$command->bindParam(':content',$content,PDO::PARAM_LOB);
					$command->execute();
				}
			}

			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			echo 'Cannot update.'.$e->getMessage();
			Yii::app()->end();
		}
	}
}
?>