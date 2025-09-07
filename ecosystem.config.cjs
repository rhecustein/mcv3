module.exports = {
    apps: [
        {
            name: "mcv3 - laravel queue",
            namespace: "default",
            script: "/usr/bin/php",
            args: "artisan queue:work --queue=high,default",
            interpreter: "none",
            exec_mode: "fork_mode",
        },
    ],
};
