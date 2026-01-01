<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Categories
        $categories = [
            ['name' => 'Web Development', 'slug' => 'web-development'],
            ['name' => 'Artificial Intelligence', 'slug' => 'artificial-intelligence'],
            ['name' => 'Database Management', 'slug' => 'database-management'],
            ['name' => 'Mobile Development', 'slug' => 'mobile-development'],
            ['name' => 'Cybersecurity', 'slug' => 'cybersecurity'],
            ['name' => 'Cloud Computing', 'slug' => 'cloud-computing'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['slug' => $cat['slug']], $cat);
        }

        // Tags
        $tags = [
            'PHP', 'Laravel', 'JavaScript', 'React', 'Vue.js', 'Python', 'TensorFlow', 'MySQL',
            'PostgreSQL', 'MongoDB', 'Android', 'iOS', 'Docker', 'AWS', 'Cybersecurity', 'Machine Learning',
            'DevOps', 'Node.js', 'TypeScript', 'Blockchain', 'IoT', 'API', 'Microservices', 'Kubernetes'
        ];

        foreach ($tags as $tagName) {
            Tag::firstOrCreate(
                ['slug' => Str::slug($tagName)],
                ['name' => $tagName, 'slug' => Str::slug($tagName)]
            );
        }

        // Posts
        $posts = [
            [
                'title' => 'Building Scalable Web Applications with Laravel',
                'content' => 'Laravel is a powerful PHP framework that helps developers build robust and scalable web applications. In this post, we explore best practices for creating maintainable code, implementing authentication, and optimizing performance.',
                'category_slug' => 'web-development',
                'tags' => ['PHP', 'Laravel', 'Web Development'],
                'thumbnail' => 'posts/tech-1.jpg',
            ],
            [
                'title' => 'Introduction to Machine Learning with Python',
                'content' => 'Machine learning is revolutionizing industries across the globe. This comprehensive guide covers the fundamentals of ML, popular algorithms, and how to get started with Python libraries like scikit-learn and TensorFlow.',
                'category_slug' => 'artificial-intelligence',
                'tags' => ['Python', 'Machine Learning', 'TensorFlow'],
                'thumbnail' => 'posts/tech-2.jpg',
            ],
            [
                'title' => 'Optimizing Database Performance',
                'content' => 'Database performance is crucial for application speed and user experience. Learn about indexing strategies, query optimization, and best practices for MySQL and PostgreSQL databases.',
                'category_slug' => 'database-management',
                'tags' => ['MySQL', 'PostgreSQL', 'Database Management'],
                'thumbnail' => 'posts/tech-3.jpg',
            ],
            [
                'title' => 'Developing Cross-Platform Mobile Apps',
                'content' => 'Cross-platform development allows you to build apps for both iOS and Android using a single codebase. Explore React Native, Flutter, and other frameworks that are shaping mobile development.',
                'category_slug' => 'mobile-development',
                'tags' => ['React', 'Android', 'iOS'],
                'thumbnail' => 'posts/tech-4.jpg',
            ],
            [
                'title' => 'Securing Your Applications Against Cyber Threats',
                'content' => 'Cybersecurity is more important than ever. This post covers essential security practices, common vulnerabilities, and tools for protecting your applications from attacks.',
                'category_slug' => 'cybersecurity',
                'tags' => ['Cybersecurity', 'Security', 'DevOps'],
                'thumbnail' => 'posts/tech-5.jpg',
            ],
            [
                'title' => 'Leveraging Cloud Computing for Modern Apps',
                'content' => 'Cloud computing offers unprecedented scalability and flexibility. Discover how platforms like AWS, Azure, and Google Cloud are transforming application development and deployment.',
                'category_slug' => 'cloud-computing',
                'tags' => ['AWS', 'Cloud Computing', 'Docker'],
                'thumbnail' => 'posts/tech-6.jpg',
            ],
            [
                'title' => 'The Future of AI in Software Development',
                'content' => 'Artificial intelligence is not just a trend - it\'s reshaping how we build software. From code generation to automated testing, AI tools are becoming indispensable for developers.',
                'category_slug' => 'artificial-intelligence',
                'tags' => ['Artificial Intelligence', 'Machine Learning', 'DevOps'],
                'thumbnail' => 'posts/tech-7.jpg',
            ],
            [
                'title' => 'Microservices Architecture: Pros and Cons',
                'content' => 'Microservices offer great flexibility but come with complexity. This in-depth analysis explores when to use microservices, implementation strategies, and common pitfalls to avoid.',
                'category_slug' => 'web-development',
                'tags' => ['Microservices', 'API', 'DevOps'],
                'thumbnail' => 'posts/tech-8.jpg',
            ],
        ];

        foreach ($posts as $postData) {
            $category = Category::where('slug', $postData['category_slug'])->first();

            $post = Post::create([
                'title' => $postData['title'],
                'slug' => Str::slug($postData['title']),
                'content' => $postData['content'],
                'thumbnail' => $postData['thumbnail'],
                'status' => 'published',
                'category_id' => $category ? $category->id : null,
            ]);

            $tagIds = [];
            foreach ($postData['tags'] as $tagName) {
                $tag = Tag::where('name', $tagName)->first();
                if ($tag) {
                    $tagIds[] = $tag->id;
                }
            }
            $post->tags()->sync($tagIds);
        }
    }
}
