<?php

$offer_end_time_insert = 'CREATE TRIGGER IF NOT EXISTS offer_end_time_insert
    BEFORE INSERT ON `offer`
    FOR EACH ROW
    SET NEW.`end_time` =  DATE_ADD(CURRENT_TIMESTAMP,INTERVAL 30 DAY);
';

$offer_end_time_update = 'CREATE TRIGGER IF NOT EXISTS offer_end_time_update
    BEFORE UPDATE ON `offer`
    FOR EACH ROW
    SET NEW.`end_time` =  DATE_ADD(CURRENT_TIMESTAMP,INTERVAL 30 DAY);
';

//make trigger for table 'rating'
// on ins del upd
// get client.id by offer_id from offer then upd field 'client.rating' where id=client.id by offer_id

$rating_insert = 'CREATE TRIGGER IF NOT EXISTS rating_insert
    AFTER INSERT ON `rating`
    FOR EACH ROW
    BEGIN
        DECLARE sum INT;
        DECLARE row_count INT;
        SELECT SUM(`rating`.`rating_value`) INTO sum FROM `rating` WHERE `rating`.`client_id_to_whom` = NEW.`client_id_to_whom`;
        SELECT COUNT(*) INTO row_count FROM `rating` WHERE `rating`.`client_id_to_whom` = NEW.`client_id_to_whom`;
        UPDATE `client` SET `client`.`rating` = (sum DIV row_count) WHERE `client`.`id` = NEW.`client_id_to_whom`;
    END
';

$rating_update = 'CREATE TRIGGER IF NOT EXISTS rating_update
    AFTER UPDATE ON `rating`
    FOR EACH ROW
    BEGIN
        DECLARE sum INT;
        DECLARE row_count INT;
        SELECT SUM(`rating`.`rating_value`) INTO sum FROM `rating` WHERE `rating`.`client_id_to_whom` = NEW.`client_id_to_whom`;
        SELECT COUNT(*) INTO row_count FROM `rating` WHERE `rating`.`client_id_to_whom` = NEW.`client_id_to_whom`;
        UPDATE `client` SET `client`.`rating` = (sum DIV row_count) WHERE `client`.`id` = NEW.`client_id_to_whom`;
    END
';
$rating_delete = 'CREATE TRIGGER IF NOT EXISTS rating_delete
    AFTER DELETE ON `rating`
    FOR EACH ROW
    BEGIN
        DECLARE sum INT;
        DECLARE row_count INT;
        SELECT SUM(`rating`.`rating_value`) INTO sum FROM `rating` WHERE `rating`.`client_id_to_whom` = OLD.`client_id_to_whom`;
        SELECT COUNT(*) INTO row_count FROM `rating` WHERE `rating`.`client_id_to_whom` = OLD.`client_id_to_whom`;
        UPDATE `client` SET `client`.`rating` = (sum DIV row_count) WHERE `client`.`id` = OLD.`client_id_to_whom`;
    END
';

$trigger_sqls = [
    'offer' => [
        $offer_end_time_insert,
        $offer_end_time_update,
    ],
    'rating' => [
        $rating_insert,
        $rating_update,
        $rating_delete,
    ],
];
