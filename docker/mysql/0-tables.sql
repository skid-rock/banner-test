-- banner table
create table banner
(
    id          int auto_increment,
    url         varchar(255)  not null,
    k           int default 1 not null,
    view_count  int default 0 not null,
    total_views int default 0 not null,
    constraint banner_id_uindex
        unique (id)
);

alter table banner
    add primary key (id);

-- banner_user table
create table banner_user
(
    banner_id  int           not null,
    user_id    varchar(255)  not null,
    view_count int default 0 not null,
    primary key (banner_id, user_id)
);
