delimiter ;

create table if not exists papervote_api_token_log (
    id varchar(36) primary key,
    created_at timestamp default current_timestamp,
    key_id varchar(255),
    token_short varchar(36),
    token varchar(2048),
    client_ip varchar(64)
);
