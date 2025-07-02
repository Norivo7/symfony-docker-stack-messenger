vcl 4.0

backend default {
    .host = "nginx";
    .port = "80";
}

sub vcl_recv {
    return (hash);
}

sub vcl_backend_response {
    set beresp.ttl = 10s;
}