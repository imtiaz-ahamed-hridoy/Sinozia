FFmpeg is not installed or not in your system PATH. Fix it in 3 steps.

---

## Step 1 — Download FFmpeg

```
1. Go to: https://www.gyan.dev/ffmpeg/builds/
2. Download: ffmpeg-release-essentials.zip
3. Extract to: C:\ffmpeg
```

Your folder structure should look like:
```
C:\ffmpeg\
└── bin\
    ├── ffmpeg.exe
    ├── ffprobe.exe
    └── ffplay.exe
```

---

## Step 2 — Add to Windows PATH

```
1. Press: Windows key + S → search "Environment Variables"
2. Click: "Edit the system environment variables"
3. Click: "Environment Variables" button
4. Under "System variables" → find "Path" → click "Edit"
5. Click "New" → type: C:\ffmpeg\bin
6. Click OK → OK → OK
```

### Verify it works — open a NEW terminal:
```bash
ffmpeg -version
# Should show: ffmpeg version 7.x.x ...
```

> Must open a new terminal — existing ones won't pick up the PATH change.

---


## Quick Verification

After PATH is set, open a new terminal in your project and test:

```bash
# Test FFmpeg directly
C:\ffmpeg\bin\ffmpeg.exe -version

# Test Laravel can see it
php artisan tinker
>>> $p = new \Symfony\Component\Process\Process(['C:\ffmpeg\bin\ffmpeg.exe', '-version']);
>>> $p->run();
>>> echo $p->getOutput();
```

