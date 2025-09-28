<?php
    include_once("/home/services/php_utils/string.util.php");

    $nhl_host = "https://www.nhl.com";
    $nhl_api_host = "https://statsapi.web.nhl.com";

    // 场上位置
    $position_config = array(
        // "" => 0, // 未知
        "Goalie" => 1, // 守门员G
        "Defenseman" => 2, // 后卫D
        "Left Defender" => 3, // 左后卫LD
        "Right Defender" => 4, // 右后卫RD
        "Forward" => 5, // 前锋F
        "Left Wing" => 6, // 左边锋LF
        "Right Wing" => 7, // 右边锋RF
        "Center" => 8, // 中锋C
    );

    // 抓取队伍列表配置
    $teams_config = array (
        'url' => "https://www.nhl.com",
        'config' => array(
            'content_pattern' => array(
                "base_pattern" => 'top-nav__secondary--teams`atop-nav__secondary-section__subsection--expansion`bLanguages',"result_pattern" => '`a',
                ),
            'delimitor' => '<li class="top-nav__secondary-section__menu__item',
            "item_patterns" => array(
                'homepage' => array(
                    "base_pattern" => '<a`ahref="`b" data-phone-href`c</li>',"result_pattern" => '`b',
                    // "base_pattern" => '<a`ahref="`b"`c</li>',"result_pattern" => '`b',
                ),
                'english_short' => array(
                    "base_pattern" => '<span`atop-nav__secondary-section__menu__item__link__team-name`b>`c</span>',"result_pattern" => '`c',
                )
            ),
        )
    );

    // 抓取队伍详情配置
    $team_config = array(
        'content_pattern' => array(
            "base_pattern" => '<head`a</body>',"result_pattern" => '`a',
            ),
        'delimitor' => 'keywords',
        "item_patterns" => array(
            'name' => array(
                "base_pattern" => '<div class="top-nav__club-logo">`a<a`btitle="`c">`d<img`e</a>`f</div>',"result_pattern" => '`c',
            ),
            'english' => array(
                "base_pattern" => '<div class="top-nav__club-logo">`a<a`btitle="`c">`d<img`e</a>`f</div>',"result_pattern" => '`c',
            ),
            'english_abbr' => array(
                "base_pattern" => 'dfpAdUnitHierarchy`acontent="`b_nhl_web"`csite_code',"result_pattern" => '`b',
            ),
            'logo' => array(
                "base_pattern" => '<div class="top-nav__club-logo">`a<img`bsrc="`c"`d</a>`f</div>',"result_pattern" => '`c',
            ),
            'nhl_team_id' => array(
                "base_pattern" => '<meta name="team_id" content="`a"`b/>',"result_pattern" => '`a',
                // "base_pattern" => '<div class="video-preview"`a<iframe`b/video/embed/t-`c/`d</iframe>',"result_pattern" => '`c',
                // "base_pattern" => '/video/embed/t-`a?`b</iframe>',"result_pattern" => '`a',
                // "base_pattern1" => 'name="clubVideos"`a/video/t-`b"',"result_pattern1" => '`b',
            ),
            'img' => array(
                "base_pattern" => 'mediawall-sm@sm`asrcset="`b"`cmedia-body',"result_pattern" => '`b',
            ),
            'img_title' => array( 
                "base_pattern" => 'media-body`a<h4 class="mediawall__kicker">`b<a`c>`d</a>`e</h4>`f<ul',"result_pattern" => '`d',
            ),
            'img_description' => array( 
                "base_pattern" => 'media-body`a<h4`d</h4>`c<p`d>`e<a`f<ul',"result_pattern" => '`e',
            ),
            'blog' => array(
                "base_pattern" => 'mixed-feed__list`a</ul>',
                "result_pattern" => '`a^^^',
                "handle_config" => array(
                    'content_pattern' => array(
                        "base_pattern" => '`a^^^',"result_pattern" => '`a',
                    ),
                    "delimitor" => '<li',
                    "item_patterns" => array(
                        'nhl_video_id' => array( 
                            "base_pattern" => 'mixed-feed__content`amixed-feed__media-overlay--video`bdata-share-url="`c/c-`d"`e</i>',"result_pattern" => '`d',
                        ),
                        'video_url' => array( 
                            "base_pattern" => 'mixed-feed__content`amixed-feed__media-overlay--video`bdata-share-url="`c"`d</i>',"result_pattern" => $nhl_host.'`c',
                        ),
                        'img_url' => array( 
                            "base_pattern" => 'mixed-feed__content`adata-srcset="http`b.jpg`cdata-sizes="auto"',"result_pattern" => 'http`b.jpg',
                        ),
                        'nhl_image_id' => array( 
                            "base_pattern" => 'mixed-feed__content`adata-srcset="http`b/photos/`c/`d.jpg`edata-sizes="auto"',"result_pattern" => '`c',
                        ),

                        'title' => array( 
                            "base_pattern" => 'mixed-feed__item-header-text`a<h4`b>`c</h4>',"result_pattern" => '`c',
                        ),
                        'description' => array( 
                            "base_pattern" => 'mixed-feed__item-header-text`a<h5`b>`c</h5>',"result_pattern" => '`c',
                        ),
                        'created_date' => array( 
                            "base_pattern" => 'mixed-feed__item-header-text`a<time datetime="`bT`c-`d">`e</time>',"result_pattern" => '`b `c',
                        )
                    ),
                ),
            ),
        ),
    );

    // 抓取视频详情配置
    $video_config = array(
        'content_pattern' => array(
            "base_pattern" => '<body`a</body>',"result_pattern" => '`a',
        ),
        'delimitor' => 'team-landing_index',
        "item_patterns" => array(
            // 标题、描述、日期、tags、缩略图、类型（根据获取视频播放url的方法得到）、vid（即c-后面的数字）、时长
            // 视频tag抓那里的？
            'name' => array( 
                "base_pattern" => '<div itemprop="video"`aitemprop="name" content="`b">`cid="app"',"result_pattern" => '`b',
            ),
            'description' => array( 
                "base_pattern" => '<div itemprop="video"`aitemprop="description" content="`b">`cid="app"',"result_pattern" => '`b',
            ),
            'created_date' => array( 
                "base_pattern" => '<div itemprop="video"`aitemprop="uploadDate" content="`bT`c.`d"`eid="app"',"result_pattern" => '`b `c',
            ),
            // 'tags' => array( 
            //     "base_pattern" => '`a',"result_pattern" => '`b',
            // ),
            'snapshot' => array( 
                "base_pattern" => '<div itemprop="video"`aitemprop="image" href="`b">`cid="app"',"result_pattern" => '`b',
                "base_pattern1" => '<meta itemprop="image" content="`a" />',"result_pattern1" => '`a',
                
            ),
            'duration' => array( 
                "base_pattern" => '<div itemprop="video"`aitemprop="duration" content="`b" />`cid="app"',"result_pattern" => '`b',
            ),
            'tags' => array( 
                "base_pattern" => 'id="content-wrap"`a"tags":`b,"id"',"result_pattern" => '`b',
            ),
            "videourls" => array(
                "base_pattern" => '"playbacks":[{`a}]',
                "result_pattern" => '[{`a}]',
            ),
        ),
    );
    
    // 抓取运动员列表配置
    $players_config = array (
        // 'url' => "https://www.nhl.com/[team]/stats",
        // 'url' => "https://statsapi.web.nhl.com/api/v1/teams/[nhl_team_id]?hydrate=franchise(roster(season=20192020,person(name,stats(splits=[yearByYear]))))",
        'url' => "https://statsapi.web.nhl.com/api/v1/teams/[nhl_team_id]?hydrate=franchise(roster(person))",
        'config' => array(
            'is_json' => true,
            'content_pattern' => array(
                "base_pattern" => '`a',"result_pattern" => '`a',
                ),
            "list_keys" => array(
                'teams',
                0,
                'franchise',
                "roster",
                "roster"
                ),
            "properties" => array(
                "person" => "person",
                ),
        )
    );

    // 抓取热门运动员列表配置
    $players_popular_config = array (
        'url' => "https://www.nhl.com/player",
        'config' => array(
            'content_pattern' => array(
                "base_pattern" => 'top-players-wrapper_inner`a<footer',"result_pattern" => '`a',
                ),
            'delimitor' => 'class="player-container"',
            "item_patterns" => array(
                'nhl_url' => array(
                    "base_pattern" => '<a`ahref="`b"`c</figure>`d</a>',"result_pattern" => $nhl_host.'`b',
                )
            ),
        )
    );

    // 抓取运动员详情配置
    $player_config = array(
        'content_pattern' => array(
            "base_pattern" => '<head`a</body>',"result_pattern" => '`a',
            ),
        'delimitor' => 'keywords',
        "item_patterns" => array(
            'name' => array(
                "base_pattern" => 'id="content-wrap"`aplayerName: \'`b\'`cplayerType`d<div',"result_pattern" => '`b',
            ),
            'birthday' => array(
                "base_pattern" => 'id="content-wrap"`a"birthDate":"`b"`c"jobTitle"`d<div',"result_pattern" => '`b',
            ),
            'birthplace' => array(
                "base_pattern" => 'player-bio__list`aBirthplace`b</span>`c</li>`d</ul>',"result_pattern" => '`c',
            ),
            'shoots' => array(
                "base_pattern" => 'player-bio__list`aShoots`b</span>`c</li>`d</ul>',"result_pattern" => '`c',
            ),
            'twitter' => array(
                "base_pattern" => 'twitter-follow-button`ahref="`b"`c</a>',"result_pattern" => '`b',
            ),
            'team' => array(
                "base_pattern" => 'content-wrap`aaffiliation":"`b",`c<div',"result_pattern" => '`b',
            ),
            'position' => array(
                "base_pattern" => 'content-wrap`ajobTitle":"`b",`c<div',"result_pattern" => '`b',
            ),
            'height' => array(
                "base_pattern" => 'content-wrap"`a"height":`b,"weight"`c<div',"result_pattern" => '`b',
            ),
            'weight' => array(
                "base_pattern" => 'content-wrap"`a"height":`b,"weight":`c}`d<div',"result_pattern" => '`c}',
            ),
            'num' => array(
                "base_pattern" => 'player-jumbotron-vitals__name-num`a | #`b</',"result_pattern" => '`b',
            ),
            'nhl_player_id' => array(
                "base_pattern" => 'content-wrap`aplayerId: \'`b\'`c<div',"result_pattern" => '`b',
            ),
            'avatar' => array(
                "base_pattern" => 'class="player-jumbotron-vitals__headshot"`a<img`bsrc="`c"`d/>`e</div>',"result_pattern" => '`c',
            ),
            'cover' => array(
                "base_pattern" => 'class="player-jumbotron-cover"`a<div`bdata-img="`c"`dplayer-jumbotron-cover__image`eclass="player-jumbotron-vitals"',"result_pattern" => '`c',
            ),
            'news' => array(
                "base_pattern" => 'news__row`anews__news-more',
                "result_pattern" => '`a^^^',
                "handle_config" => array(
                    'content_pattern' => array(
                        "base_pattern" => '`a^^^',"result_pattern" => '`a',
                    ),
                    "delimitor" => 'news__news-item',
                    "item_patterns" => array(
                        'video_url' => array( 
                            "base_pattern" => '<a`ahref="`b"`cnews__news-thumb video`d</a>',"result_pattern" => $nhl_host . '`b',
                        ),
                        'nhl_video_id' => array( 
                            "base_pattern" => '<a`ahref="`b/c-`c"`dnews__news-thumb video`e</a>',"result_pattern" => '`c',
                        )
                    ),
                ),
            ),
        ),
    );

    // 抓取比赛配置
    $scores_config = array (
        // 'url' => "https://www.nhl.com/scores/[date]",
        // 'url' => "https://statsapi.web.nhl.com/api/v1/schedule?startDate=[date]&endDate=[date]&hydrate=team(leaders(categories=[points,goals,assists],gameTypes=[R])),linescore,broadcasts(all),tickets,game(content(media(epg),highlights(scoreboard)),seriesSummary),radioBroadcasts,metadata,decisions,scoringplays,seriesSummary(series)",
        'url' => $nhl_api_host."/api/v1/schedule?startDate=[date]&endDate=[date]&hydrate=game(content(media(epg))),scoringplays,linescore",
        // 'start_data' => "2019-10-01", // 包含本日期
        'start_data' => "2019-12-15", // 包含本日期
        'end_data' => "2020-10-01", // 不包含本日期
        'config' => array(
            'is_json' => true,
            "list_keys" => array(
                'dates',
                0,
                'games'
                ),
            "properties" => array(
                "gamePk" => "gamePk",
                "gamecenter" => "link",
                "season" => "season",
                "gameDate" => "gameDate",
                "status" => "status",
                "content" => "content",
                "scoringPlays" => "scoringPlays",
                "linescore" => "linescore",
                "teams" => "teams"
                ),
            'content_pattern' => array(
                "base_pattern" => '`a',"result_pattern" => '`a',
                ),
        )
    );
    
    // 抓取比赛视频列表配置
    $game_video_list_config = array (
        'url' => $nhl_host."/video/t-[topicList]",
        'config' => array(
            'content_pattern' => array(
                "base_pattern" => 'class="carousel__container`a<div class="swiper-pagination"></div>',"result_pattern" => '`a^^^',
                ),
            'delimitor' => 'js-carousel__container',
            "item_patterns" => array(
                'title' => array(
                    // "base_pattern" => '<a`ahref="`b" data-phone-href`c</li>',"result_pattern" => '`b',
                    "base_pattern" => '<h5`a>`b</h5>',"result_pattern" => '`b',
                ),
                'video_list' => array(
                    "base_pattern" => 'carousel__items`a^^^',
                    "result_pattern" => '`a^^^',
                    "handle_config" => array(
                        'content_pattern' => array(
                            "base_pattern" => '`a^^^',"result_pattern" => '`a',
                        ),
                        "delimitor" => 'id="carousel__item--',
                        "item_patterns" => array(
                            'video_url' => array( 
                                "base_pattern" => 'data-asset-id="`a"`bclass="video-preview',"result_pattern" => $nhl_host . '/video/c-`a',
                            ),
                            'nhl_video_id' => array( 
                                "base_pattern" => 'data-asset-id="`a"`bclass="video-preview',"result_pattern" => '`a',
                            )
                        ),
                    ),
                )
            ),
        )
    );

    // $game_scores_config = array (
    //     'is_json' => true,
    //     "list_keys" => array(
    //         'gameData',
    //         ),
    //     "properties" => array(
    //         "status" => "status",
    //         // "liveData" => "liveData",
    //         "teams" => "teams"
    //         ),
    //     'content_pattern' => array(
    //         "base_pattern" => '`a',"result_pattern" => '`a',
    //         ),
    // );
    

    $geme_state_config = array(
        // "" => 0,
        "Final" => 1,
        // "" => 2,
        "Postponed" => 3,
    );
?>