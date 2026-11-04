-- Pull in the wezterm API
local wezterm = require 'wezterm'

-- This will hold the configuration.
local config = wezterm.config_builder()

-- This is where you actually apply your config choices.

-- For example, changing the initial geometry for new windows:
config.initial_cols = 120
config.initial_rows = 28

-- or, changing the font size and color scheme.
config.font_size = 12
config.color_scheme = 'OneHalfDark'
config.font = wezterm.font('JetBrains Mono')
config.window_background_opacity = 0.5
config.wayland_window_background_blur = true

local act = wezterm.action

config.keys = {
  -- ESC+CR (Alt+Enter) is what Claude Code and readline can tell apart from a plain Enter.
  { key = 'Enter', mods = 'SHIFT', action = act.SendString '\x1b\r' },
  -- \x17 is Ctrl+W, the word-erase control code understood by readline and most TUIs.
  { key = 'Backspace', mods = 'CTRL', action = act.SendString '\x17' },
  { key = 't', mods = 'CTRL', action = act.SpawnTab 'CurrentPaneDomain' },
  { key = 'w', mods = 'CTRL', action = act.CloseCurrentTab { confirm = true } },
}

-- Finally, return the configuration to wezterm:
return config
