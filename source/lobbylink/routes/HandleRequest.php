<?php

require_once __DIR__."/../MinimalRouter.php";

class HandleRequest
{
    public function execute(Request &$request, Response &$response)
    {
        if ($request->Method == "GET")
        {
            $this->handle_get_request($request, $response);
        }
        else
        {
            $this->handle_post_request($request, $response);
        }
    }

    private function handle_get_request(Request &$request, Response &$response)
    {
        $template = new Template(__DIR__."/../html/");
        $template->load("index.html");
        $template->replace("/../../assets/", "./../assets/");

        $template->assign("VALUE", "");
        $template->assign("REPLACE", "");
        $template->assign("FOOTER", 'style="margin-top: 13.55rem !important"');

        $response->ContentType = ContentType::TEXT_HTML;
        $response->Data = $template->display();

        $response->enable_caching();
    }

    private function handle_post_request(Request &$request, Response &$response)
    {
        $template = new Template(__DIR__."/../html/");
        $template->load("index.html");
        $template->replace("/../../assets/", "./../assets/");

        $steam = $request->get_data("profile");

        $template->assign("VALUE", $steam);

        if (empty($steam))
        {
            $template->assign("REPLACE", $this->get_no_data_out("Missing Steam profile URL, ID or name!"));
            $template->assign("FOOTER", 'style="margin-top: 2rem !important"');
        }
        else
        {
            $lobby_id = $this->get_lobby_id($steam);

            if (empty($lobby_id))
            {
                $template->assign("REPLACE", $this->get_error_out());
                $template->assign("FOOTER", 'style="margin-top: 2rem !important"');
            }
            else
            {
                $template->assign("REPLACE", $this->get_success_out("steam://joinlobby/730/" . $lobby_id));
                $template->assign("FOOTER", 'style="margin-top: 2.15rem !important"');
            }
        }

        $response->ContentType = ContentType::TEXT_HTML;
        $response->Data = $template->display();

        $response->disable_caching();
    }

    private function get_lobby_id($profile)
    {
        if (ctype_digit($profile))
        {
            $profile = "https://steamcommunity.com/profiles/". $profile;
        }
        else if (ctype_alnum($profile))
        {
            $profile = "https://steamcommunity.com/id/". $profile;
        }
        else if (!strstr($profile, "steamcommunity.com"))
        {
            return false;
        }

        $content = file_get_contents($profile);

        $pattern = '<a href="steam://joinlobby/730/(.+/.+)".+class="btn_green_white';

        if (preg_match("@" . $pattern . "@mi", $content, $matches))
        {
            return $matches[1];
        }
        else
        {
            return false;
        }
    }

    private function get_success_out($link)
    {
        $out = '<div class="container mt-4"><div class="card card-shadow"><div class="card-header card-header-border bg-secondary">';
        $out .= '<h3 class="text-center text-success"><i class="fas fa-check"></i> Success</h3></div><div class="card-body">';
        $out .= '<div class="small-box"><label for="lobbyLink"><i class="fas fa-link"></i> Link</label>';
        $out .= '<input id="lobbyLink" type="url" class="form-control" value="' . $link . '" onclick="clipboard(this);showTooltip(this)" onblur="hideTooltip(this)" data-toggle="tooltip" data-trigger="manual" title="Copied" readonly>';
        $out .= '</div></div></div></div>';

        return $out;
    }

    private function get_error_out()
    {
        $out = '<div class="container mt-4"><div class="card card-shadow"><div class="card-header card-header-border bg-secondary">';
        $out .= '<h3 class="text-center text-danger"><i class="fas fa-exclamation-triangle"></i> Error</h3>';
        $out .= '</div><div class="card-body">';
        $out .= '<div class="small-box"><p>Please verify the following requirements:</p>';
        $out .= '<ul><li>Is your steam profile public?</li><li>Is your lobby public?</li><li>Is the entered profile valid?</li></ul>';
        $out .= '</div></div></div></div>';

        return $out;
    }

    private function get_no_data_out($message)
    {
        $out = '<div class="container mt-4"><div class="card card-shadow"><div class="card-header card-header-border bg-secondary">';
        $out .= '<h3 class="text-center text-danger"><i class="fas fa-exclamation-triangle"></i> Error</h3>';
        $out .= '</div><div class="card-body">';
        $out .= '<p class="small-box">' . $message . '</p>';
        $out .= '</div></div></div>';
        
        return $out;
    }
}

?>