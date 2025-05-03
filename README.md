# Multi-page Twig Static Site

## How to use:
1. Run `composer install`
2. Run `php render.php`
3. Check the `build/` folder for home.html, about.html, contact.html


# Project Setup
cd my-theme-project
composer require "twig/twig:^3.0"

# Tailwind Setup
cd tailwind
npm init -y
npm install -D tailwindcss
npx tailwindcss init
npx tailwindcss -i ./tailwind/input.css -o ../assets/style.css --watch

# Back to project root
cd ..


# use components
# 🔹 Step 1: ফাইল তৈরি করুন:
📁 templates/components/button.twig


<a href="{{ url }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
  {{ label }}
</a>
# 🔹 Step 2: hero.twig বা অন্য কোথাও ব্যবহার করুন:

{% include "components/button.twig" with {
  url: "/signup",
  label: "Join Now"
} %}


# Build Render
php render.php

# example
php make-section.php hero
🎁 আপনি পাবেন:
templates/partials/hero.twig ✅
data/hero.json ✅



## Assets:

- CSS: /assets/style.css, custom.css
- Images: /assets/images/*
- Include them in twig using relative path: /assets/...

# 🔁 Render Modes:
| মোড      | ব্যাখ্যা                                                          |
| -------- | ----------------------------------------------------------------- |
| `single` | শুধু `page.twig` রেন্ডার করবে                                     |
| `all`    | সব `templates/**/*.twig` রেন্ডার করে build/min/enc ফোল্ডারে রাখবে |
| `custom` | নির্দিষ্ট ফাইল map করে slug অনুযায়ী রেন্ডার                       |

#🧹 Folder Reset Options:
| Command                  | কাজ                                        |
| ------------------------ | ------------------------------------------ |
| `php render.php`         | ফোল্ডার থাকলে reset করার জন্য জিজ্ঞেস করবে |
| `php render.php --force` | অটো reset করে দেবে, prompt ছাড়াই ✅         |


#🧩 package.json Scripts Guide:
| Script             | Description                                   |
| ------------------ | --------------------------------------------- |
| `npm run tailwind` | Tailwind CSS কে watch করে CSS build করে       |
| `npm run dev`      | twig/json পরিবর্তন হলে `render.php --force` auto রান  |
| `npm run serve`    | BrowserSync দিয়ে live browser refresh চালু    |
| `npm run start`    | উপরোক্ত সবকিছু একসাথে চালু হয় (All-in-one 🔥) |


