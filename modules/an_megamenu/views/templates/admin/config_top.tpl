<link href="https://fonts.googleapis.com/css?family=Ubuntu:400,700&display=swap" rel="stylesheet">
<style>
.an_panel {
    border-radius: 5px;
    margin: 0 4px 39px;
    font-family: 'Ubuntu', sans-serif;
}
.an_panel-link {
    text-decoration: underline!important;
}

.an_panel_info {
    font-family: 'Ubuntu', sans-serif;
    display: flex;
    padding: 0 5px;
    margin-bottom: 19px;
}
.an_panel_info-item {
    background: #fff;
    box-shadow: 0px 1px 1px 0px rgba(0, 0, 0, 0.1);
    border-radius: 2px;
    padding: 18px 36px 18px 18px;
    max-width: 330px;
    width: 100%;
    margin-right: 20px;
    margin-bottom: 20px;
}
.an_panel_info-item:last-child {
    margin-right: 0;
}
.an_panel_info-item-contact {
    border-left: 3px solid #21a6cb;
}
.an_panel_info-item-rate {
    border-left: 3px solid #fed500;
}
.an_panel_info-item-docs {
    border-left: 3px solid #e56b93;
}
.an_panel_info-item h2 {
    font-size: 18px;
    font-family: 'Ubuntu', sans-serif;
	font-weight: bold;
    margin: 0 0 7px;
}
.an_panel_info-item p {
    font-size: 14px;
    line-height: 24px;
    margin: 0;
}
.an_panel_info .grade {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
    margin-top: 13px;
}


@media (max-width: 1366px) {
    .an_panel_info-item {
        max-width: 50%;
    }
}
@media (max-width: 767px) {
    .an_panel_info-item {
        max-width: 100%;
        margin-right: 0;
    }
    .an_panel_info {
        flex-direction: column;
    }
}
@media (max-width: 480px) {
    .an_panel_info-item {
        margin-right: 0;
    }
    .an_panel_modules-item {
        flex-direction: column;
        padding: 20px 0;
        position: relative;
    }
    .an_panel_modules-item-title {
        position: static;
    }
    .an_panel_modules-disabled-flag {
        top: 20px;
    }
}
</style>


{$contact_us = 'http://bit.ly/2OT7uaZ'}


<div class="an_panel_info">
    <div class="an_panel_info-item an_panel_info-item-rate">
        <h2><a class="an_panel-link" href="{$configure}">To Main Menu</a></h2>
		<p>Open the <a href="{$configure}" class="an_panel-link">Main Menu</a> of the module</p>
    </div>
    <div class="an_panel_info-item an_panel_info-item-contact">
        <h2>Contact Us</h2>
        <p><a class="an_panel-link" href="{$contact_us}" target="_blank">Contact us</a> on any question or problem with the module</p>
    </div>
    <div class="an_panel_info-item an_panel_info-item-docs">
        <h2><a class="an_panel-link" href="{$modulePath}/doc/readme_en.pdf" target="_blank">Documentation</a></h2>
        <p>If you need help or any question / problem watch our documentation </p>
    </div>
</div>