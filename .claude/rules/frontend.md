---
description: 前端资源规范，编辑 JS/CSS/Blade 视图时适用
globs: ["resources/**/*.js", "resources/**/*.css", "resources/**/*.scss", "resources/**/*.blade.php"]
---

# 前端规范

- `resources/dist/` 是编译产物，禁止直接编辑
- 修改前端源码后需重新编译: `npx mix --production`
- Layer 弹窗必须设置 `shade: [0.3, '#000']`
- 生产构建不生成 sourcemap
