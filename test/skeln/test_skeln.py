import importlib.util
import importlib.machinery
import os
import shutil
import subprocess
import sys
import pytest
from pathlib import Path


SCRIPT_PATH = Path(__file__).parent.parent.parent / "modules" / "scripts" / "utilux" / "utilux-skeln"


def load_skeln():
    loader = importlib.machinery.SourceFileLoader("skeln", str(SCRIPT_PATH))
    spec = importlib.util.spec_from_loader("skeln", loader)
    mod = importlib.util.module_from_spec(spec)
    loader.exec_module(mod)
    return mod


skeln = load_skeln()


# --- Helpers ---

def make_env(tmp_path):
    config_dir = tmp_path / "config"
    root = tmp_path / "root"
    root.mkdir(parents=True)
    skeln.ensure_config(str(config_dir))
    return config_dir, root


def make_args(config_dir, root, command, target=None):
    class Args:
        pass
    args = Args()
    args.config_dir = str(config_dir)
    args.root = str(root)
    args.command = command
    args.name = None
    if target is not None:
        args.target = target
    return args


# --- config dir resolution ---

def test_config_dir_is_in_utilux_appdata(monkeypatch, tmp_path):
    monkeypatch.setenv("UTILUX_APPDATA_PATH", str(tmp_path))
    monkeypatch.setattr(sys, "argv", ["skeln", "list"])
    args = skeln.parse_args()
    assert args.config_dir == str(tmp_path / "skeln")


def test_config_dir_exits_without_utilux(monkeypatch):
    monkeypatch.delenv("UTILUX_APPDATA_PATH", raising=False)
    with pytest.raises(SystemExit) as exc:
        skeln.utilux_config_dir()
    assert exc.value.code == 1


def test_root_uses_option(monkeypatch, tmp_path):
    monkeypatch.setenv("UTILUX_APPDATA_PATH", str(tmp_path))
    monkeypatch.setattr(sys, "argv", ["skeln", "--root", str(tmp_path), "list"])
    args = skeln.parse_args()
    assert args.root == str(tmp_path)


def test_root_fallback_to_slash(monkeypatch, tmp_path):
    monkeypatch.setenv("UTILUX_APPDATA_PATH", str(tmp_path))
    monkeypatch.setattr(sys, "argv", ["skeln", "list"])
    args = skeln.parse_args()
    assert args.root == "/"


# --- check_platform ---

def test_check_platform_exits_on_non_linux(monkeypatch):
    monkeypatch.setattr(sys, "platform", "darwin")
    with pytest.raises(SystemExit) as exc:
        skeln.check_platform()
    assert exc.value.code == 1


def test_check_platform_passes_on_linux(monkeypatch):
    monkeypatch.setattr(sys, "platform", "linux")
    skeln.check_platform()


# --- check_not_root ---

def test_check_not_root_exits_when_root(monkeypatch):
    monkeypatch.setattr(os, "geteuid", lambda: 0)
    with pytest.raises(SystemExit) as exc:
        skeln.check_not_root()
    assert exc.value.code == 1


def test_check_not_root_passes_for_normal_user(monkeypatch):
    monkeypatch.setattr(os, "geteuid", lambda: 1000)
    skeln.check_not_root()


# --- ensure_config ---

def test_ensure_config_creates_structure(tmp_path):
    config_dir = tmp_path / "nested" / "skeln"
    skeln.ensure_config(str(config_dir))
    assert config_dir.is_dir()
    assert (config_dir / "store").is_dir()
    assert (config_dir / "registry").is_file()


def test_ensure_config_does_not_overwrite_existing_file(tmp_path):
    config_dir = tmp_path / "skeln"
    config_file = config_dir / "registry"
    config_dir.mkdir()
    config_file.write_text("existing content")
    skeln.ensure_config(str(config_dir))
    assert config_file.read_text() == "existing content"


def test_ensure_config_idempotent(tmp_path):
    config_dir = tmp_path / "skeln"
    skeln.ensure_config(str(config_dir))
    skeln.ensure_config(str(config_dir))


# --- mount ---

def test_mount_moves_dir_and_creates_symlink(tmp_path):
    config_dir, root = make_env(tmp_path)
    target_dir = root / "etc" / "myconf"
    target_dir.mkdir(parents=True)
    (target_dir / "settings.conf").write_text("data")

    args = make_args(config_dir, root, "mount", "/etc/myconf")
    skeln.cmd_mount(args)

    assert target_dir.is_symlink()
    assert (target_dir / "settings.conf").read_text() == "data"

    entries = skeln.read_config(config_dir / "registry")
    assert "/etc/myconf" in entries.values()

def test_mount_refuses_nonexistent_target(tmp_path):
    config_dir, root = make_env(tmp_path)
    args = make_args(config_dir, root, "mount", "/etc/ghost")
    with pytest.raises(SystemExit):
        skeln.cmd_mount(args)


def test_mount_refuses_existing_symlink(tmp_path):
    config_dir, root = make_env(tmp_path)
    target = root / "etc" / "myconf"
    target.parent.mkdir(parents=True)
    target.symlink_to("/tmp")
    args = make_args(config_dir, root, "mount", "/etc/myconf")
    with pytest.raises(SystemExit):
        skeln.cmd_mount(args)


def test_mount_custom_name(tmp_path):
    config_dir, root = make_env(tmp_path)
    target_dir = root / "etc" / "myconf"
    target_dir.mkdir(parents=True)

    args = make_args(config_dir, root, "mount", "/etc/myconf")
    args.name = "my_conf"
    skeln.cmd_mount(args)

    entries = skeln.read_config(config_dir / "registry")
    assert "my_conf" in entries


def test_mount_custom_name_invalid(tmp_path):
    config_dir, root = make_env(tmp_path)
    target_dir = root / "etc" / "myconf"
    target_dir.mkdir(parents=True)

    args = make_args(config_dir, root, "mount", "/etc/myconf")
    args.name = "My-Invalid Name!"
    with pytest.raises(SystemExit):
        skeln.cmd_mount(args)


def test_mount_custom_name_duplicate(tmp_path):
    config_dir, root = make_env(tmp_path)
    for name in ("confA", "confB"):
        (root / name).mkdir(parents=True)

    args_a = make_args(config_dir, root, "mount", "/confA")
    args_a.name = "shared_name"
    skeln.cmd_mount(args_a)

    args_b = make_args(config_dir, root, "mount", "/confB")
    args_b.name = "shared_name"
    with pytest.raises(SystemExit):
        skeln.cmd_mount(args_b)


def test_mount_refuses_duplicate(tmp_path):
    config_dir, root = make_env(tmp_path)
    target_dir = root / "etc" / "myconf"
    target_dir.mkdir(parents=True)

    args = make_args(config_dir, root, "mount", "/etc/myconf")
    skeln.cmd_mount(args)

    # Re-mount : la cible est maintenant un symlink, doit refuser
    with pytest.raises(SystemExit):
        skeln.cmd_mount(args)


# --- unmount ---

def test_unmount_restores_dir_and_removes_symlink(tmp_path):
    config_dir, root = make_env(tmp_path)
    target_dir = root / "etc" / "myconf"
    target_dir.mkdir(parents=True)
    (target_dir / "settings.conf").write_text("data")

    mount_args = make_args(config_dir, root, "mount", "/etc/myconf")
    skeln.cmd_mount(mount_args)

    unmount_args = make_args(config_dir, root, "unmount", "/etc/myconf")
    skeln.cmd_unmount(unmount_args)

    assert target_dir.is_dir()
    assert not target_dir.is_symlink()
    assert (target_dir / "settings.conf").read_text() == "data"

    entries = skeln.read_config(config_dir / "registry")
    assert str(target_dir) not in entries.values()


def test_unmount_by_hex_id(tmp_path):
    config_dir, root = make_env(tmp_path)
    target_dir = root / "etc" / "myconf"
    target_dir.mkdir(parents=True)

    skeln.cmd_mount(make_args(config_dir, root, "mount", "/etc/myconf"))
    hex_id = list(skeln.read_config(config_dir / "registry").keys())[0]

    skeln.cmd_unmount(make_args(config_dir, root, "unmount", hex_id))

    assert target_dir.is_dir()
    assert not target_dir.is_symlink()


def test_unmount_refuses_unknown_target(tmp_path):
    config_dir, root = make_env(tmp_path)
    args = make_args(config_dir, root, "unmount", "/etc/ghost")
    with pytest.raises(SystemExit):
        skeln.cmd_unmount(args)


# --- list ---

def test_list_empty(tmp_path, capsys):
    config_dir, root = make_env(tmp_path)
    args = make_args(config_dir, root, "list")
    skeln.cmd_list(args)
    assert "No mounted paths" in capsys.readouterr().out


def test_list_shows_installed_status(tmp_path, capsys):
    config_dir, root = make_env(tmp_path)
    d = root / "confA"
    d.mkdir()
    skeln.cmd_mount(make_args(config_dir, root, "mount", "/confA"))

    skeln.cmd_list(make_args(config_dir, root, "list"))
    out = capsys.readouterr().out
    assert str(root / "confA") in out
    assert "installed" in out


def test_list_shows_missing_status(tmp_path, capsys):
    config_dir, root = make_env(tmp_path)
    d = root / "confA"
    d.mkdir()
    skeln.cmd_mount(make_args(config_dir, root, "mount", "/confA"))
    (root / "confA").unlink()  # supprime le symlink

    skeln.cmd_list(make_args(config_dir, root, "list"))
    out = capsys.readouterr().out
    assert "missing" in out


# --- install ---

def test_install_creates_missing_symlink(tmp_path, capsys):
    config_dir, root = make_env(tmp_path)
    target_dir = root / "etc" / "myconf"
    target_dir.mkdir(parents=True)

    skeln.cmd_mount(make_args(config_dir, root, "mount", "/etc/myconf"))
    target_dir.unlink()  # simule un symlink absent (nouvelle machine)

    skeln.cmd_install(make_args(config_dir, root, "install"))

    assert target_dir.is_symlink()
    out = capsys.readouterr().out
    assert "[LINK]" in out


def test_install_skips_already_installed(tmp_path, capsys):
    config_dir, root = make_env(tmp_path)
    target_dir = root / "etc" / "myconf"
    target_dir.mkdir(parents=True)

    skeln.cmd_mount(make_args(config_dir, root, "mount", "/etc/myconf"))
    skeln.cmd_install(make_args(config_dir, root, "install"))

    out = capsys.readouterr().out
    assert "[OK]" in out
    assert "[LINK]" not in out


def test_install_replaces_empty_dir_with_symlink(tmp_path, capsys):
    config_dir, root = make_env(tmp_path)
    target_dir = root / "etc" / "myconf"
    target_dir.mkdir(parents=True)

    skeln.cmd_mount(make_args(config_dir, root, "mount", "/etc/myconf"))
    target_dir.unlink()
    target_dir.mkdir()  # dossier vide à la place du symlink

    skeln.cmd_install(make_args(config_dir, root, "install"))

    assert target_dir.is_symlink()
    assert "[LINK]" in capsys.readouterr().out


def test_install_warns_on_nonempty_conflicting_path(tmp_path, capsys):
    config_dir, root = make_env(tmp_path)
    target_dir = root / "etc" / "myconf"
    target_dir.mkdir(parents=True)

    skeln.cmd_mount(make_args(config_dir, root, "mount", "/etc/myconf"))
    target_dir.unlink()
    target_dir.mkdir()
    (target_dir / "leftover.txt").write_text("data")  # dossier non-vide

    skeln.cmd_install(make_args(config_dir, root, "install"))

    err = capsys.readouterr().err
    assert "WARN" in err
    assert not target_dir.is_symlink()


def test_install_empty_config(tmp_path, capsys):
    config_dir, root = make_env(tmp_path)
    skeln.cmd_install(make_args(config_dir, root, "install"))
    assert "Nothing to install" in capsys.readouterr().out


# --- mount/unmount round-trip avec --root ---

@pytest.mark.parametrize("target_path", ["/etc/foo", "/var/lib/bar"])
def test_roundtrip(tmp_path, target_path):
    config_dir, root = make_env(tmp_path)
    actual = root / Path(target_path).relative_to("/")
    actual.mkdir(parents=True)
    (actual / "file.txt").write_text("hello")

    skeln.cmd_mount(make_args(config_dir, root, "mount", target_path))
    assert actual.is_symlink()

    skeln.cmd_unmount(make_args(config_dir, root, "unmount", target_path))
    assert actual.is_dir()
    assert not actual.is_symlink()
    assert (actual / "file.txt").read_text() == "hello"


# --- zip/unzip ---

requires_7z = pytest.mark.skipif(shutil.which("7z") is None, reason="7z is not installed")


def mount_sample(tmp_path, machine):
    config_dir, root = make_env(tmp_path / machine)
    target_dir = root / "etc" / "myconf"
    target_dir.mkdir(parents=True)
    (target_dir / "file.txt").write_text("hello")
    (target_dir / "link").symlink_to("file.txt")
    skeln.cmd_mount(make_args(config_dir, root, "mount", "/etc/myconf"))
    return config_dir, root


def zip_args(config_dir, root, output, password=False):
    args = make_args(config_dir, root, "zip")
    args.output = str(output)
    args.password = password
    return args


def unzip_args(config_dir, root, archive, patches=()):
    args = make_args(config_dir, root, "unzip")
    args.archive = str(archive)
    args.patch = list(patches)
    return args


@requires_7z
@pytest.mark.parametrize("password", [False, True])
def test_zip_unzip_roundtrip(tmp_path, monkeypatch, password):
    monkeypatch.setattr(skeln.getpass, "getpass", lambda prompt: "s3cret")
    config_dir, root = mount_sample(tmp_path, "old")
    archive = tmp_path / "skeln.zip"
    skeln.cmd_zip(zip_args(config_dir, root, archive, password))

    new_config_dir, new_root = make_env(tmp_path / "new")
    skeln.cmd_unzip(unzip_args(new_config_dir, new_root, archive))

    target_dir = new_root / "etc" / "myconf"
    assert target_dir.is_symlink()
    assert (target_dir / "file.txt").read_text() == "hello"
    assert (target_dir / "link").is_symlink()
    assert skeln.read_config(new_config_dir / "registry") == skeln.read_config(config_dir / "registry")
    assert not any(p.name.startswith(".unzip-") for p in new_config_dir.iterdir())


@requires_7z
def test_zip_is_encrypted_with_password(tmp_path, monkeypatch):
    monkeypatch.setattr(skeln.getpass, "getpass", lambda prompt: "s3cret")
    config_dir, root = mount_sample(tmp_path, "old")
    archive = tmp_path / "skeln.zip"
    skeln.cmd_zip(zip_args(config_dir, root, archive, password=True))
    assert skeln.is_encrypted(archive)


@requires_7z
def test_unzip_wrong_password_leaves_skeln_empty(tmp_path, monkeypatch):
    monkeypatch.setattr(skeln.getpass, "getpass", lambda prompt: "s3cret")
    config_dir, root = mount_sample(tmp_path, "old")
    archive = tmp_path / "skeln.zip"
    skeln.cmd_zip(zip_args(config_dir, root, archive, password=True))

    monkeypatch.setattr(skeln.getpass, "getpass", lambda prompt: "wrong")
    new_config_dir, new_root = make_env(tmp_path / "new")
    with pytest.raises(SystemExit):
        skeln.cmd_unzip(unzip_args(new_config_dir, new_root, archive))

    assert sorted(p.name for p in new_config_dir.iterdir()) == ["registry", "store"]
    assert not any((new_config_dir / "store").iterdir())


def test_zip_password_mismatch_exits(tmp_path, monkeypatch):
    answers = iter(["one", "two"])
    monkeypatch.setattr(skeln.getpass, "getpass", lambda prompt: next(answers))
    with pytest.raises(SystemExit):
        skeln.ask_password(confirm=True)


@requires_7z
def test_zip_exits_when_nothing_mounted(tmp_path):
    config_dir, root = make_env(tmp_path)
    with pytest.raises(SystemExit):
        skeln.cmd_zip(zip_args(config_dir, root, tmp_path / "skeln.zip"))
    assert not (tmp_path / "skeln.zip").exists()


@requires_7z
def test_zip_exits_when_output_exists(tmp_path):
    config_dir, root = mount_sample(tmp_path, "old")
    archive = tmp_path / "skeln.zip"
    archive.write_text("existing")
    with pytest.raises(SystemExit):
        skeln.cmd_zip(zip_args(config_dir, root, archive))
    assert archive.read_text() == "existing"


@requires_7z
def test_unzip_exits_when_skeln_holds_data(tmp_path):
    config_dir, root = mount_sample(tmp_path, "old")
    archive = tmp_path / "skeln.zip"
    skeln.cmd_zip(zip_args(config_dir, root, archive))

    registry_before = (config_dir / "registry").read_text()
    with pytest.raises(SystemExit):
        skeln.cmd_unzip(unzip_args(config_dir, root, archive))
    assert (config_dir / "registry").read_text() == registry_before


@requires_7z
def test_unzip_exits_on_foreign_archive(tmp_path):
    (tmp_path / "other.txt").write_text("not skeln")
    archive = tmp_path / "other.zip"
    subprocess.run(["7z", "a", "-tzip", "-bso0", str(archive), "other.txt"], cwd=tmp_path, check=True)

    config_dir, root = make_env(tmp_path / "new")
    with pytest.raises(SystemExit):
        skeln.cmd_unzip(unzip_args(config_dir, root, archive))
    assert sorted(p.name for p in config_dir.iterdir()) == ["registry", "store"]


# --- unzip --patch ---

@pytest.mark.parametrize("raw_patch", ["/home/yonis", "home/yonis=/home/root", "/home/yonis=home/root"])
def test_parse_patches_exits_on_malformed_patch(raw_patch):
    with pytest.raises(SystemExit):
        skeln.parse_patches([raw_patch])


def test_parse_patches_exits_on_duplicate_prefix():
    with pytest.raises(SystemExit):
        skeln.parse_patches(["/home/yonis=/a", "/home/yonis/=/b"])


@pytest.mark.parametrize("target, expected", [
    ("/home/yon/sub", "/home/yon"),
    ("/home/yon", "/home/yon"),
    ("/home/yonis/Documents/", "/home/yonis/Documents"),
    ("/home/yonis/Doc", "/home/yonis"),
    ("/home/yonis/Pictures", "/home/yonis"),
    ("/home/yonis/Documents/work", "/home/yonis/Documents"),
    ("/etc/nginx", None),
])
def test_find_patch_matches_whole_directories_longest_first(target, expected):
    patches = skeln.parse_patches(["/home/yon=/a", "/home/yonis=/b", "/home/yonis/Documents=/c"])
    found = skeln.find_patch(target, patches)
    assert (str(found) if found else None) == expected


def test_apply_patches_rewrites_registry(tmp_path, capsys):
    registry = tmp_path / "registry"
    skeln.write_config(registry, {"a": "/home/yonis/Documents/", "b": "/home/yonis/Pictures/", "c": "/etc/nginx"})

    skeln.apply_patches(registry, skeln.parse_patches(["/home/yonis=/home/devuser"]))

    assert skeln.read_config(registry) == {"a": "/home/devuser/Documents", "b": "/home/devuser/Pictures", "c": "/etc/nginx"}
    assert "[PATCH] /home/yonis/Documents/ → /home/devuser/Documents" in capsys.readouterr().out


def test_apply_patches_exits_on_unused_patch_without_writing(tmp_path):
    registry = tmp_path / "registry"
    skeln.write_config(registry, {"a": "/home/yonis/Documents/"})

    with pytest.raises(SystemExit):
        skeln.apply_patches(registry, skeln.parse_patches(["/home/yonis=/home/devuser", "/home/yon=/x"]))

    assert skeln.read_config(registry) == {"a": "/home/yonis/Documents/"}


@requires_7z
def test_unzip_with_patch_links_patched_paths(tmp_path):
    config_dir, root = mount_sample(tmp_path, "old")
    archive = tmp_path / "skeln.zip"
    skeln.cmd_zip(zip_args(config_dir, root, archive))

    new_config_dir, new_root = make_env(tmp_path / "new")
    skeln.cmd_unzip(unzip_args(new_config_dir, new_root, archive, ["/etc=/opt"]))

    assert (new_root / "opt" / "myconf").is_symlink()
    assert (new_root / "opt" / "myconf" / "file.txt").read_text() == "hello"
    assert not (new_root / "etc").exists()


@requires_7z
def test_unzip_with_unused_patch_leaves_skeln_empty(tmp_path):
    config_dir, root = mount_sample(tmp_path, "old")
    archive = tmp_path / "skeln.zip"
    skeln.cmd_zip(zip_args(config_dir, root, archive))

    new_config_dir, new_root = make_env(tmp_path / "new")
    with pytest.raises(SystemExit):
        skeln.cmd_unzip(unzip_args(new_config_dir, new_root, archive, ["/etc=/opt", "/var=/srv"]))

    assert sorted(p.name for p in new_config_dir.iterdir()) == ["registry", "store"]
    assert not any((new_config_dir / "store").iterdir())
