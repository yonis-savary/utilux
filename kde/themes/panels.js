var plasma = getApiVersion(1);

panels().forEach(panel => panel.remove());

var layout = {
    "panels": [
        {
            "alignment": "center",
            "applets": [
                {
                    "config": {
                        "/": {
                            "popupHeight": "400",
                            "popupWidth": "560"
                        },
                        "/General": {
                            "favoritesPortedToKAstats": "true",
                            "icon": "start-here-kubuntu",
                            "showAppsByName": "true"
                        },
                        "/Shortcuts": {
                            "global": "Alt+F1"
                        }
                    },
                    "plugin": "org.kde.plasma.kickoff"
                },
                {
                    "config": {},
                    "plugin": "org.kde.plasma.marginsseparator"
                },
                {
                    "config": {},
                    "plugin": "org.kde.plasma.panelspacer"
                },
                {
                    "config": {},
                    "plugin": "org.kde.plasma.systemtray"
                },
                {
                    "config": {
                        "/": {
                            "popupHeight": "400",
                            "popupWidth": "560"
                        },
                        "/Appearance": {
                            "fontWeight": "400",
                            "showDate": "true"
                        },
                        "/ConfigDialog": {
                            "DialogHeight": "630",
                            "DialogWidth": "810"
                        }
                    },
                    "plugin": "org.kde.plasma.digitalclock"
                },
                {
                    "config": {},
                    "plugin": "org.kde.plasma.showdesktop"
                }
            ],
            "config": {
                "/": {
                    "formfactor": "2",
                    "immutability": "1",
                    "wallpaperplugin": "org.kde.image"
                }
            },
            "height": 2,
            "hiding": "normal",
            "location": "top",
            "maximumLength": 106.66666666666667,
            "minimumLength": 106.66666666666667,
            "offset": 0
        },
        {
            "alignment": "center",
            "applets": [
                {
                    "config": {
                        "/": {
                            "launchers": "applications:systemsettings.desktop,applications:org.kubuntu.manage-software.desktop,applications:org.kde.dolphin.desktop,applications:org.kde.konsole.desktop,preferred://browser"
                        },
                        "/ConfigDialog": {
                            "DialogHeight": "630",
                            "DialogWidth": "810"
                        },
                        "/General": {
                            "groupingStrategy": "0",
                            "launchers": "applications:org.kde.konsole.desktop,applications:code.desktop,applications:slack.desktop",
                            "middleClickAction": "Close"
                        }
                    },
                    "plugin": "org.kde.plasma.icontasks"
                }
            ],
            "config": {
                "/": {
                    "formfactor": "2",
                    "immutability": "1",
                    "wallpaperplugin": "org.kde.image"
                }
            },
            "height": 5.111111111111111,
            "hiding": "autohide",
            "location": "bottom",
            "maximumLength": 106.66666666666667,
            "minimumLength": 106.66666666666667,
            "offset": 0
        }
    ],
    "serializationFormatVersion": "1"
};

var panelSettings = [
    {
        "screen": 0,
        "lengthMode": "fill",
        "floating": false,
        "hiding": "none",
        "opacity": "adaptive"
    },
    {
        "screen": 0,
        "lengthMode": "fit",
        "floating": true,
        "hiding": "autohide",
        "opacity": "adaptive"
    }
];

plasma.loadSerializedLayout(layout);

// The serialized layout carries neither the screen, length mode, floating state nor opacity of panels.
panels().slice(-panelSettings.length).forEach((panel, index) => {
    panel.screen = Math.min(panelSettings[index].screen, screenCount - 1);
    panel.lengthMode = panelSettings[index].lengthMode;
    panel.floating = panelSettings[index].floating;
    panel.hiding = panelSettings[index].hiding;
    panel.opacity = panelSettings[index].opacity;
});
