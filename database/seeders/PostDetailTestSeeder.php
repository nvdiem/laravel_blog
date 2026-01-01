<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostDetailTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();
        $tags = Tag::all();
        $thumbnails = ['posts/tech-1.jpg', 'posts/tech-2.jpg', 'posts/tech-3.jpg', 'posts/tech-4.jpg', 'posts/tech-5.jpg'];

        $postsData = [
            [
                'title' => 'Advanced Laravel Eloquent Relationships: Beyond the Basics',
                'content' => '<h2>Understanding Eloquent Relationships</h2><p>Eloquent provides a beautiful, simple ActiveRecord implementation for working with your database. Each database table has a corresponding "Model" which is used to interact with that table. Models allow you to query for data in your tables, as well as insert new records into the table.</p><h3>One-to-One Relationships</h3><p>A one-to-one relationship is a very basic relation. For example, a User model might be associated with one Phone. To define this relationship, we place a phone method on the User model.</p><pre><code class="language-php">class User extends Model
{
    public function phone()
    {
        return $this->hasOne(Phone::class);
    }
}</code></pre><h2>Polymorphic Relationships</h2><p>A polymorphic relationship allows a model to belong to more than one other model on a single association. For example, imagine you have a photos table and you want to be able to associate photos with posts and users.</p><h3>Defining Polymorphic Relationships</h3><ul><li>Use morphTo() method on the owning model</li><li>Use morphMany() or morphOne() on the associated model</li><li>Requires type and id columns</li></ul><pre><code class="language-javascript">const photo = {
    id: 1,
    path: \'path/to/photo.jpg\',
    imageable_type: \'App\\Models\\Post\',
    imageable_id: 1
};</code></pre><p>This flexibility allows for more dynamic and reusable code structures in your Laravel applications.</p>',
                'category_slug' => 'web-development',
                'tags' => ['Laravel', 'PHP', 'Eloquent'],
                'thumbnail' => 'posts/tech-1.jpg',
            ],
            [
                'title' => 'Building Scalable React Applications with Hooks',
                'content' => '<h2>The Evolution of React</h2><p>React has evolved significantly since its initial release. Hooks were introduced in React 16.8 as a way to use state and other React features without writing a class component. This marked a significant shift in how React applications are built.</p><h3>Understanding useState</h3><p>The useState hook allows you to add state to functional components. It returns an array with two elements: the current state value and a function to update it.</p><pre><code class="language-javascript">import React, { useState } from \'react\';

function Counter() {
    const [count, setCount] = useState(0);

    return (
        <div>
            <p>Count: {count}</p>
            <button onClick={() => setCount(count + 1)}>
                Increment
            </button>
        </div>
    );
}</code></pre><h2>Custom Hooks for Reusability</h2><p>Custom hooks allow you to extract component logic into reusable functions. This promotes code reuse and makes your components cleaner.</p><h3>Benefits of Custom Hooks</h3><ul><li>Share logic between components</li><li>Simplify complex components</li><li>Improve testability</li><li>Encourage separation of concerns</li></ul><pre><code class="language-javascript">function useLocalStorage(key, initialValue) {
    const [storedValue, setStoredValue] = useState(() => {
        try {
            const item = window.localStorage.getItem(key);
            return item ? JSON.parse(item) : initialValue;
        } catch (error) {
            return initialValue;
        }
    });

    const setValue = (value) => {
        try {
            setStoredValue(value);
            window.localStorage.setItem(key, JSON.stringify(value));
        } catch (error) {
            console.error(error);
        }
    };

    return [storedValue, setValue];
}</code></pre><p>Custom hooks are a powerful pattern for building maintainable React applications.</p>',
                'category_slug' => 'web-development',
                'tags' => ['React', 'JavaScript', 'Hooks'],
                'thumbnail' => 'posts/tech-2.jpg',
            ],
            [
                'title' => 'Docker Compose: Orchestrating Multi-Container Applications',
                'content' => '<h2>Introduction to Docker Compose</h2><p>Docker Compose is a tool for defining and running multi-container Docker applications. With Compose, you use a YAML file to configure your application\'s services, networks, and volumes.</p><h3>Basic docker-compose.yml Structure</h3><p>A typical docker-compose.yml file defines services, networks, and volumes. Here\'s a simple example for a Laravel application.</p><pre><code class="language-yaml">version: \'3.8\'
services:
  app:
    build: .
    ports:
      - "8000:8000"
    volumes:
      - .:/var/www/html
    depends_on:
      - db

  db:
    image: mysql:8.0
    environment:
      MYSQL_DATABASE: laravel
      MYSQL_USER: laravel
      MYSQL_PASSWORD: secret
    volumes:
      - db_data:/var/lib/mysql

volumes:
  db_data:</code></pre><h2>Common Docker Compose Commands</h2><h3>Starting Services</h3><ul><li>docker-compose up -d (start in background)</li><li>docker-compose up --build (rebuild images)</li><li>docker-compose logs (view logs)</li><li>docker-compose down (stop and remove)</li></ul><h2>Networking in Docker Compose</h2><p>By default, Compose sets up a single network for your app. Each container for a service joins the default network and is both reachable by other containers on that network, and discoverable by them at a hostname identical to the container name.</p><pre><code class="language-yaml">networks:
  frontend:
    driver: bridge
  backend:
    driver: bridge

services:
  web:
    networks:
      - frontend
      - backend
  api:
    networks:
      - backend
  db:
    networks:
      - backend</code></pre><p>This network configuration allows for better isolation and security in complex applications.</p>',
                'category_slug' => 'cloud-computing',
                'tags' => ['Docker', 'DevOps', 'Containerization'],
                'thumbnail' => 'posts/tech-3.jpg',
            ],
            [
                'title' => 'Machine Learning Model Deployment with FastAPI',
                'content' => '<h2>Why FastAPI for ML Deployment?</h2><p>FastAPI is a modern, fast web framework for building APIs with Python 3.7+ based on standard Python type hints. It\'s particularly well-suited for deploying machine learning models due to its performance and ease of use.</p><h3>Key Features</h3><ul><li>Fast: Very high performance, on par with NodeJS and Go</li><li>Fast to code: Increase development speed by 200-300%</li><li>Fewer bugs: Reduce human-induced errors by 40%</li><li>Intuitive: Great editor support with auto-completion</li><li>Short: Minimize code duplication</li></ul><h2>Basic FastAPI Application</h2><pre><code class="language-python">from fastapi import FastAPI

app = FastAPI()

@app.get("/")
async def read_root():
    return {"Hello": "World"}

@app.get("/items/{item_id}")
async def read_item(item_id: int, q: str = None):
    return {"item_id": item_id, "q": q}</code></pre><h3>Loading and Using ML Models</h3><p>FastAPI can easily serve machine learning models. Here\'s an example using scikit-learn and joblib.</p><pre><code class="language-python">from fastapi import FastAPI
from pydantic import BaseModel
import joblib
import numpy as np

app = FastAPI()

# Load the trained model
model = joblib.load(\'model.pkl\')

class PredictionRequest(BaseModel):
    features: list

@app.post("/predict")
async def predict(request: PredictionRequest):
    # Make prediction
    prediction = model.predict([request.features])
    return {"prediction": prediction.tolist()}</code></pre><h2>Model Serialization</h2><p>Before deploying, you need to serialize your trained model. Popular options include:</p><ul><li>joblib (for scikit-learn models)</li><li>pickle (Python standard library)</li><li>ONNX (framework-agnostic format)</li></ul><pre><code class="language-python">import joblib

# Save model
joblib.dump(model, \'model.pkl\')

# Load model
loaded_model = joblib.load(\'model.pkl\')</code></pre><p>FastAPI provides an excellent platform for deploying machine learning models with high performance and developer-friendly features.</p>',
                'category_slug' => 'artificial-intelligence',
                'tags' => ['Python', 'Machine Learning', 'FastAPI'],
                'thumbnail' => 'posts/tech-4.jpg',
            ],
            [
                'title' => 'Advanced TypeScript Patterns for Large-Scale Applications',
                'content' => '<h2>TypeScript in Modern Development</h2><p>TypeScript has become an essential tool for building large-scale JavaScript applications. Its static typing system helps catch errors early and improves code maintainability.</p><h3>Advanced Types</h3><p>TypeScript offers powerful type constructs that go beyond basic types.</p><h2>Conditional Types</h2><p>Conditional types allow you to create types that depend on other types.</p><pre><code class="language-typescript">type IsString<T> = T extends string ? true : false;

type A = IsString<string>;  // true
type B = IsString<number>;  // false</code></pre><h3>Mapped Types</h3><p>Mapped types allow you to create new types by transforming properties of existing types.</p><pre><code class="language-typescript">type Readonly<T> = {
    readonly [P in keyof T]: T[P];
};

type Partial<T> = {
    [P in keyof T]?: T[P];
};

interface User {
    name: string;
    age: number;
}

type ReadonlyUser = Readonly<User>;
type PartialUser = Partial<User>;</code></pre><h2>Utility Types</h2><p>TypeScript provides several built-in utility types for common type transformations.</p><h3>Common Utility Types</h3><ul><li>Partial<T> - Makes all properties optional</li><li>Required<T> - Makes all properties required</li><li>Readonly<T> - Makes all properties readonly</li><li>Pick<T, K> - Picks specific properties</li><li>Omit<T, K> - Omits specific properties</li></ul><pre><code class="language-typescript">// Example usage
interface User {
    id: number;
    name: string;
    email: string;
    createdAt: Date;
}

// Create a type for user creation (without id and timestamps)
type CreateUserInput = Omit<User, \'id\' | \'createdAt\'>;

// Create a type for user updates (all optional except id)
type UpdateUserInput = Partial<Omit<User, \'id\'>> & { id: number };</code></pre><h2>Advanced Patterns</h2><h3>Branded Types</h3><p>Branded types help prevent mixing incompatible values of the same primitive type.</p><pre><code class="language-typescript">type UserId = string & { readonly __brand: \'UserId\' };
type PostId = string & { readonly __brand: \'PostId\' };

function createUserId(id: string): UserId {
    return id as UserId;
}

function createPostId(id: string): PostId {
    return id as PostId;
}

// This will cause a type error
const userId: UserId = createUserId(\'123\');
const postId: PostId = userId; // Error!</code></pre><p>These advanced TypeScript patterns enable building more robust and maintainable large-scale applications.</p>',
                'category_slug' => 'web-development',
                'tags' => ['TypeScript', 'JavaScript', 'Web Development'],
                'thumbnail' => 'posts/tech-5.jpg',
            ],
            [
                'title' => 'Database Indexing Strategies for High-Performance Applications',
                'content' => '<h2>The Importance of Database Indexing</h2><p>Database indexing is crucial for optimizing query performance, especially as your application grows and handles more data. Proper indexing can reduce query execution time from minutes to milliseconds.</p><h3>Types of Database Indexes</h3><ul><li><strong>B-Tree Indexes</strong>: The most common type, good for equality and range queries</li><li><strong>Hash Indexes</strong>: Excellent for equality queries, poor for range queries</li><li><strong>GIN Indexes</strong>: Good for indexing array and full-text search</li><li><strong>GiST Indexes</strong>: Useful for geometric data and full-text search</li><li><strong>SP-GiST Indexes</strong>: For space-partitioned trees</li></ul><h2>Creating Indexes in SQL</h2><p>Different database systems have different syntax for creating indexes.</p><h3>MySQL Example</h3><pre><code class="language-sql">-- Single column index
CREATE INDEX idx_users_email ON users (email);

-- Composite index
CREATE INDEX idx_posts_category_status ON posts (category_id, status);

-- Unique index
CREATE UNIQUE INDEX idx_users_username ON users (username);

-- Partial index (PostgreSQL)
CREATE INDEX idx_active_users ON users (email) WHERE active = true;</code></pre><h2>Index Best Practices</h2><h3>When to Create Indexes</h3><ul><li>Columns frequently used in WHERE clauses</li><li>Columns used in JOIN conditions</li><li>Columns used in ORDER BY clauses</li><li>Columns used in GROUP BY clauses</li></ul><h3>When NOT to Create Indexes</h3><ul><li>Columns with low selectivity</li><li>Tables with frequent writes</li><li>Small tables</li><li>Columns rarely used in queries</li></ul><h2>Monitoring Index Performance</h2><p>Regular monitoring helps ensure your indexes remain effective.</p><h3>Useful Queries</h3><pre><code class="language-sql">-- Check index usage (PostgreSQL)
SELECT
    schemaname,
    tablename,
    indexname,
    idx_scan,
    idx_tup_read,
    idx_tup_fetch
FROM pg_stat_user_indexes
ORDER BY idx_scan DESC;

-- Check unused indexes
SELECT
    indexname,
    tablename
FROM pg_stat_user_indexes
WHERE idx_scan = 0;</code></pre><p>Remember that while indexes improve read performance, they can slow down write operations. Always monitor and adjust your indexing strategy based on your application\'s specific needs.</p>',
                'category_slug' => 'database-management',
                'tags' => ['Database', 'MySQL', 'PostgreSQL'],
                'thumbnail' => 'posts/tech-1.jpg',
            ],
        ];

        foreach ($postsData as $postData) {
            $category = Category::where('slug', $postData['category_slug'])->first();

            $slug = Str::slug($postData['title']);
            $originalSlug = $slug;
            $counter = 1;
            while (Post::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            $post = Post::firstOrCreate(
                ['slug' => $slug],
                [
                    'title' => $postData['title'],
                    'slug' => $slug,
                    'content' => $postData['content'],
                    'thumbnail' => $postData['thumbnail'] ?? null,
                    'status' => 'published',
                    'category_id' => $category ? $category->id : 1,
                    'seo_title' => $postData['title'],
                    'seo_description' => Str::limit(strip_tags($postData['content']), 160),
                ]
            );

            if ($post->wasRecentlyCreated) {
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
}
