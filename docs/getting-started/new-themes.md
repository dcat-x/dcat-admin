# 新增主题

Dcat Admin X 新增了 19 个基于 Tailwind CSS 的配色主题和 1 个 Gray 深色主题,提供更丰富的视觉选择。

## 可用主题列表

### 原有主题

- `default` - 默认蓝色主题
- `blue` - 蓝色主题
- `blue-light` - 浅蓝色主题
- `green` - 绿色主题

### 新增 Tailwind CSS 主题

基于 Tailwind CSS 色板的 19 个主题:

| 主题名称 | 色系 | 示例色值 |
|---------|------|---------|
| `slate` | 石板灰 | #64748b |
| `zinc` | 锌灰 | #71717a |
| `neutral` | 中性灰 | #737373 |
| `stone` | 石头灰 | #78716c |
| `red` | 红色 | #ef4444 |
| `orange` | 橙色 | #f97316 |
| `amber` | 琥珀色 | #f59e0b |
| `yellow` | 黄色 | #eab308 |
| `lime` | 青柠色 | #84cc16 |
| `emerald` | 翡翠绿 | #10b981 |
| `teal` | 蓝绿色 | #14b8a6 |
| `cyan` | 青色 | #06b6d4 |
| `sky` | 天空蓝 | #0ea5e9 |
| `indigo` | 靛蓝 | #6366f1 |
| `violet` | 紫罗兰 | #8b5cf6 |
| `purple` | 紫色 | #a855f7 |
| `fuchsia` | 紫红色 | #d946ef |
| `pink` | 粉色 | #ec4899 |
| `rose` | 玫瑰红 | #f43f5e |

### 深色主题

- `gray` - 深色 Header 主题(现代化深色导航栏)

## 切换主题

### 方法一:配置文件

编辑 `config/admin.php`:

```php
'layout' => [
    'color' => 'gray',  // 修改为你想要的主题
],
```

### 方法二:页面切换

1. 登录后台
2. 点击右上角用户菜单
3. 选择"设置"
4. 在"主题颜色"下拉框中选择主题
5. 点击"提交"

## 主题特点

### Gray 主题特色

Gray 主题是专为现代化界面设计的深色主题:

- **深色导航栏**: Header 使用深灰色背景
- **扁平化设计**: 移除了阴影效果,更加简洁
- **小圆角**: 按钮和卡片使用较小的圆角值
- **现代化组件**: 配合 modern.scss 的 shadcn 风格组件

### Tailwind CSS 主题

基于 Tailwind CSS 色板的主题具有以下特点:

- **色彩丰富**: 19 种精心设计的配色方案
- **统一风格**: 所有主题使用相同的设计语言
- **响应式**: 完美适配各种屏幕尺寸
- **可定制**: 易于扩展和自定义

## 主题预览

### Gray 主题效果

```
┌────────────────────────────────────────┐
│  深色导航栏 (#2d3748)                    │
│  ┌──────┬──────┬──────┐                │
│  │ Logo │ 菜单 │ 用户 │                │
│  └──────┴──────┴──────┘                │
├────────────────────────────────────────┤
│                                        │
│  白色内容区                             │
│  ┌──────────────────────────────────┐ │
│  │  卡片内容                        │ │
│  │  扁平化设计,无阴影                │ │
│  └──────────────────────────────────┘ │
│                                        │
└────────────────────────────────────────┘
```

### Tailwind 主题色彩

不同主题会影响以下UI元素的颜色:

- 主按钮 (Primary Button)
- 链接文本
- 选中状态
- 进度条
- Badge 标签
- 图表颜色

## 自定义主题

### 创建新主题

1. 在 `resources/sass/themes/` 目录下创建主题文件:

```scss
// resources/sass/themes/_mytheme.scss

// 定义主题主色调
$primary: #your-color;
$primary-darken: darken($primary, 10%);

// 导入基础样式
@import "../variables";
@import "../custom";
```

2. 在 `webpack.mix.js` 中注册主题:

```javascript
const themes = {
    // ... 现有主题
    'mytheme': '#your-color',
};
```

3. 在 `src/Color.php` 中添加主题定义:

```php
public static function all(): array
{
    return [
        // ... 现有主题
        'mytheme' => '我的主题',
    ];
}
```

4. 编译资源:

```bash
npm run prod:all
```

### 覆盖主题样式

在 `resources/sass/custom/_custom.scss` 中覆盖样式:

```scss
// 自定义按钮样式
.btn-primary {
    border-radius: 0.25rem;
    font-weight: 600;
}

// 自定义卡片样式
.card {
    border: none;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
```

## 现代化 UI 组件

配合 Gray 主题,系统提供了一套现代化的 UI 组件类。

### 按钮变体

```html
<!-- Ghost 按钮 -->
<button class="btn btn-ghost">Ghost Button</button>

<!-- Subtle 按钮 -->
<button class="btn btn-subtle">Subtle Button</button>
```

### Badge 变体

```html
<!-- Outline Badge -->
<span class="badge badge-outline">Outline</span>

<!-- Soft Badge -->
<span class="badge badge-soft">Soft</span>
```

### 骨架屏加载

```html
<div class="skeleton skeleton-text"></div>
<div class="skeleton skeleton-button"></div>
<div class="skeleton skeleton-avatar"></div>
```

### 工具类

```html
<!-- 圆角 -->
<div class="rounded-sm">小圆角</div>
<div class="rounded-md">中圆角</div>
<div class="rounded-lg">大圆角</div>

<!-- 阴影 -->
<div class="shadow-sm">小阴影</div>
<div class="shadow-md">中阴影</div>
<div class="shadow-lg">大阴影</div>

<!-- 间距 -->
<div class="gap-1">间距 1</div>
<div class="gap-2">间距 2</div>

<!-- 文本截断 -->
<p class="line-clamp-1">单行截断</p>
<p class="line-clamp-3">三行截断</p>
```

## 编译主题

### 编译单个主题

```bash
npm run dev          # 开发模式,编译 Gray 主题
npm run prod         # 生产模式,编译 Gray 主题
```

### 编译所有主题

```bash
npm run dev:all      # 开发模式,编译所有主题
npm run prod:all     # 生产模式,编译所有主题
```

### 监听文件变化

```bash
npm run watch        # 监听文件变化,自动编译
```

## 注意事项

1. **主题切换**: 切换主题后需要刷新页面才能看到效果
2. **缓存清理**: 修改主题后建议清理浏览器缓存
3. **编译顺序**: 新增主题后需要先编译再使用
4. **兼容性**: 确保 CSS 变量在目标浏览器中受支持
5. **性能**: 多主题会增加 CSS 文件大小,按需编译使用的主题

## 主题配色建议

### 专业商务
- `slate`, `zinc`, `gray`, `neutral` - 适合企业后台

### 活力创意
- `orange`, `yellow`, `lime` - 适合创意类应用

### 沉稳可靠
- `blue`, `indigo`, `cyan` - 适合金融、医疗

### 热情洋溢
- `red`, `pink`, `rose` - 适合社交、电商

### 清新自然
- `green`, `emerald`, `teal` - 适合环保、健康

### 优雅高贵
- `purple`, `violet`, `fuchsia` - 适合奢侈品、艺术

## 迁移说明

从原 Dcat Admin 迁移:

- 原有主题 (`default`, `blue`, `blue-light`, `green`) 保持兼容
- 新增主题需要重新编译前端资源
- `gray` 主题为默认主题,如需使用其他主题请修改配置
