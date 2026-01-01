<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DummyPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();
        $tags = Tag::all();
        $thumbnails = ['posts/tech-1.jpg', 'posts/tech-2.jpg', 'posts/tech-3.jpg', 'posts/tech-4.jpg', 'posts/tech-5.jpg', 'posts/tech-6.jpg', 'posts/tech-7.jpg', 'posts/tech-8.jpg'];

        $postsData = [
            [
                'title' => 'Understanding RESTful APIs in Laravel',
                'content' => '<h2>What are RESTful APIs?</h2><p>RESTful APIs are the backbone of modern web development. They allow different applications to communicate seamlessly.</p><h3>Key Principles</h3><ul><li>Stateless</li><li>Client-Server Architecture</li><li>Uniform Interface</li></ul><h2>Implementing in Laravel</h2><p>Laravel provides excellent support for building REST APIs with resource controllers.</p><pre><code>$router->resource(\'posts\', \'PostController\');</code></pre><p>This single line creates all CRUD routes for posts.</p>',
            ],
            [
                'title' => 'Database Optimization Techniques',
                'content' => '<h2>Indexing Strategies</h2><p>Proper indexing can dramatically improve query performance.</p><h3>Types of Indexes</h3><ul><li>B-tree Indexes</li><li>Hash Indexes</li><li>GIN Indexes</li><li>Composite Indexes</li></ul><h2>Query Optimization</h2><p>Use EXPLAIN to analyze query execution plans.</p><pre><code>EXPLAIN SELECT * FROM posts WHERE status = \'published\';</code></pre><p>This helps identify bottlenecks in your database queries.</p>',
            ],
            [
                'title' => 'Introduction to Docker for Developers',
                'content' => '<h2>Why Docker?</h2><p>Docker eliminates environment inconsistencies by containerizing applications.</p><h3>Basic Commands</h3><ul><li>docker build</li><li>docker run</li><li>docker-compose up</li><li>docker ps</li></ul><h2>Dockerfile Example</h2><pre><code>FROM php:8.1-fpm
COPY . /var/www/html
RUN composer install</code></pre><p>This creates a PHP container with your application.</p>',
            ],
            [
                'title' => 'Machine Learning with Python: Getting Started',
                'content' => '<h2>Popular Libraries</h2><p>Python has become the go-to language for machine learning.</p><h3>Essential Libraries</h3><ul><li>NumPy - Numerical computing</li><li>Pandas - Data manipulation</li><li>Scikit-learn - ML algorithms</li><li>TensorFlow - Deep learning</li></ul><h2>Hello World Example</h2><pre><code>import numpy as np
print("Hello, Machine Learning!")</code></pre><p>Start with simple data analysis before diving into complex models.</p>',
            ],
            [
                'title' => 'Cybersecurity Best Practices for Web Applications',
                'content' => '<h2>Common Vulnerabilities</h2><p>Understanding common threats is the first step to securing your applications.</p><h3>OWASP Top 10</h3><ul><li>SQL Injection</li><li>Cross-Site Scripting (XSS)</li><li>Cross-Site Request Forgery (CSRF)</li><li>Insecure Direct Object References</li></ul><h2>Defense Strategies</h2><p>Always validate and sanitize user input.</p><pre><code>$cleanInput = filter_var($input, FILTER_SANITIZE_STRING);</code></pre><p>Use prepared statements for database queries.</p>',
            ],
            [
                'title' => 'Building Real-time Applications with WebSockets',
                'content' => '<h2>WebSocket Protocol</h2><p>WebSockets provide full-duplex communication channels over a single TCP connection.</p><h3>Use Cases</h3><ul><li>Chat applications</li><li>Live notifications</li><li>Real-time dashboards</li><li>Collaborative editing</li></ul><h2>Laravel Broadcasting</h2><p>Laravel makes WebSocket broadcasting easy.</p><pre><code>broadcast(new MessageSent($message));</code></pre><p>This broadcasts events to connected clients automatically.</p>',
            ],
            [
                'title' => 'Microservices Architecture: Pros and Cons',
                'content' => '<h2>Benefits of Microservices</h2><p>Microservices offer better scalability and maintainability.</p><h3>Advantages</h3><ul><li>Independent deployment</li><li>Technology diversity</li><li>Team autonomy</li><li>Fault isolation</li></ul><h2>Challenges</h2><p>Complexity in communication and data consistency.</p><pre><code>// Service communication
axios.post(\'http://user-service/users\', userData);</code></pre><p>Use API gateways for centralized communication.</p>',
            ],
            [
                'title' => 'Advanced Git Techniques for Developers',
                'content' => '<h2>Branching Strategies</h2><p>Effective branching workflows improve team collaboration.</p><h3>Popular Strategies</h3><ul><li>Git Flow</li><li>GitHub Flow</li><li>Trunk-based development</li></ul><h2>Useful Commands</h2><pre><code>git rebase -i HEAD~3  # Interactive rebase
git cherry-pick <commit>  # Apply specific commits</code></pre><p>Master these commands to become a Git power user.</p>',
            ],
            [
                'title' => 'GraphQL vs REST: Choosing the Right API Approach',
                'content' => '<h2>REST Limitations</h2><p>Traditional REST APIs can lead to over/under-fetching of data.</p><h3>REST Issues</h3><ul><li>Multiple endpoints for different views</li><li>Fixed data structure</li><li>Versioning challenges</li></ul><h2>GraphQL Advantages</h2><p>Query exactly what you need.</p><pre><code>query {
  user(id: 1) {
    name
    email
  }
}</code></pre><p>Single endpoint, flexible queries.</p>',
            ],
            [
                'title' => 'DevOps Culture and Practices',
                'content' => '<h2>DevOps Principles</h2><p>DevOps is about culture, not just tools.</p><h3>Core Practices</h3><ul><li>Continuous Integration</li><li>Continuous Deployment</li><li>Infrastructure as Code</li><li>Monitoring and Logging</li></ul><h2>Tools Ecosystem</h2><pre><code># CI/CD Pipeline
stages:
  - build
  - test
  - deploy</code></pre><p>Automate everything from code to production.</p>',
            ],
            [
                'title' => 'Building Progressive Web Apps (PWAs)',
                'content' => '<h2>PWA Features</h2><p>PWAs combine the best of web and mobile apps.</p><h3>Key Features</h3><ul><li>Offline functionality</li><li>Push notifications</li><li>Installable</li><li>Fast loading</li></ul><h2>Service Workers</h2><pre><code>// Register service worker
navigator.serviceWorker.register(\'/sw.js\');</code></pre><p>Enable offline capabilities and background sync.</p>',
            ],
            [
                'title' => 'Blockchain Technology Fundamentals',
                'content' => '<h2>How Blockchain Works</h2><p>Blockchain is a distributed ledger technology.</p><h3>Key Components</h3><ul><li>Blocks</li><li>Chains</li><li>Consensus mechanisms</li><li>Cryptographic hashing</li></ul><h2>Smart Contracts</h2><p>Self-executing contracts on the blockchain.</p><pre><code>pragma solidity ^0.8.0;
contract SimpleStorage {
    uint storedData;
}</code></pre><p>Write contracts in Solidity for Ethereum.</p>',
            ],
            [
                'title' => 'Testing Strategies for Modern Applications',
                'content' => '<h2>Test Pyramid</h2><p>Balance different types of tests for comprehensive coverage.</p><h3>Test Types</h3><ul><li>Unit Tests</li><li>Integration Tests</li><li>E2E Tests</li></ul><h2>Laravel Testing</h2><pre><code>// Feature test example
public function test_user_can_create_post()
{
    $this->post(\'/posts\', $data)
         ->assertStatus(201);
}</code></pre><p>Use PHPUnit for robust testing in Laravel.</p>',
            ],
            [
                'title' => 'Serverless Computing with AWS Lambda',
                'content' => '<h2>Serverless Benefits</h2><p>No server management, automatic scaling.</p><h3>Advantages</h3><ul><li>Cost-effective</li><li>Auto-scaling</li><li>Reduced operational overhead</li></ul><h2>Function Example</h2><pre><code>exports.handler = async (event) => {
    return {
        statusCode: 200,
        body: JSON.stringify(\'Hello from Lambda!\')
    };
};</code></pre><p>Deploy functions that run on demand.</p>',
            ],
            [
                'title' => 'Mobile App Development with React Native',
                'content' => '<h2>Why React Native?</h2><p>Write once, deploy to iOS and Android.</p><h3>Benefits</h3><ul><li>Code reuse</li><li>Native performance</li><li>Hot reloading</li></ul><h2>Component Example</h2><pre><code>import React from \'react\';
import { View, Text } from \'react-native\';

const App = () => (
  <View>
    <Text>Hello React Native!</Text>
  </View>
);</code></pre><p>Build native mobile apps with JavaScript.</p>',
            ],
            [
                'title' => 'Data Structures and Algorithms for Interviews',
                'content' => '<h2>Essential Data Structures</h2><p>Master these for coding interviews and efficient programming.</p><h3>Common Structures</h3><ul><li>Arrays and Strings</li><li>Linked Lists</li><li>Stacks and Queues</li><li>Trees and Graphs</li><li>Hash Tables</li></ul><h2>Algorithm Categories</h2><p>Sorting, searching, dynamic programming, etc.</p><pre><code>// Binary search implementation
function binarySearch(arr, target) {
  let left = 0;
  let right = arr.length - 1;
  
  while (left <= right) {
    const mid = Math.floor((left + right) / 2);
    if (arr[mid] === target) return mid;
    if (arr[mid] < target) left = mid + 1;
    else right = mid - 1;
  }
  return -1;
}</code></pre><p>Practice these algorithms regularly.</p>',
            ],
            [
                'title' => 'Kubernetes for Container Orchestration',
                'content' => '<h2>Kubernetes Benefits</h2><p>Automate deployment, scaling, and management of containerized applications.</p><h3>Key Features</h3><ul><li>Auto-scaling</li><li>Load balancing</li><li>Self-healing</li><li>Service discovery</li></ul><h2>Pod Definition</h2><pre><code>apiVersion: v1
kind: Pod
metadata:
  name: my-pod
spec:
  containers:
  - name: my-container
    image: nginx</code></pre><p>Define your applications declaratively.</p>',
            ],
            [
                'title' => 'TypeScript: JavaScript with Type Safety',
                'content' => '<h2>TypeScript Advantages</h2><p>Add static typing to JavaScript for better development experience.</p><h3>Benefits</h3><ul><li>Early error detection</li><li>Better IDE support</li><li>Improved refactoring</li><li>Enhanced readability</li></ul><h2>Type Annotations</h2><pre><code>function greet(name: string): string {
  return `Hello, ${name}!`;
}

interface User {
  id: number;
  name: string;
  email: string;
}</code></pre><p>Catch errors at compile time instead of runtime.</p>',
            ],
            [
                'title' => 'The Evolution of Frontend Frameworks',
                'content' => '<h2>From jQuery to Modern Frameworks</h2><p>Frontend development has evolved significantly over the years.</p><h3>Timeline</h3><ul><li>jQuery (2006)</li><li>AngularJS (2010)</li><li>React (2013)</li><li>Vue.js (2014)</li><li>Svelte (2016)</li></ul><h2>Component-Based Architecture</h2><p>Modern frameworks emphasize reusable components.</p><pre><code>// React component
const Button = ({ children, onClick }) => (
  <button onClick={onClick}>
    {children}
  </button>
);</code></pre><p>Build UIs with composable, maintainable components.</p>',
            ],
            [
                'title' => 'Database Design Principles',
                'content' => '<h2>Normalization</h2><p>Organize data to reduce redundancy and improve integrity.</p><h3>Normal Forms</h3><ul><li>1NF: Atomic values</li><li>2NF: No partial dependencies</li><li>3NF: No transitive dependencies</li></ul><h2>Relationships</h2><p>Understand one-to-one, one-to-many, and many-to-many relationships.</p><pre><code>-- User-Post relationship
CREATE TABLE posts (
  id INT PRIMARY KEY,
  user_id INT,
  title VARCHAR(255),
  FOREIGN KEY (user_id) REFERENCES users(id)
);</code></pre><p>Design schemas that reflect real-world relationships.</p>',
            ],
            [
                'title' => 'API Rate Limiting and Throttling',
                'content' => '<h2>Why Rate Limiting?</h2><p>Protect your APIs from abuse and ensure fair usage.</p><h3>Common Strategies</h3><ul><li>Fixed window</li><li>Sliding window</li><li>Token bucket</li><li>Leaky bucket</li></ul><h2>Laravel Implementation</h2><pre><code>// Route throttling
Route::middleware(\'throttle:60,1\')->group(function () {
    Route::get(\'/api/data\', \'ApiController@data\');
});</code></pre><p>Laravel provides built-in throttling middleware.</p>',
            ],
            [
                'title' => 'Continuous Integration and Deployment (CI/CD)',
                'content' => '<h2>CI/CD Benefits</h2><p>Automate the software delivery process.</p><h3>CI/CD Pipeline</h3><ul><li>Source control</li><li>Build automation</li><li>Automated testing</li><li>Deployment</li><li>Monitoring</li></ul><h2>GitHub Actions Example</h2><pre><code>name: CI/CD
on: [push]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
    - uses: actions/checkout@v2
    - name: Run tests
      run: npm test</code></pre><p>Automate your entire development workflow.</p>',
            ],
            [
                'title' => 'Web Performance Optimization Techniques',
                'content' => '<h2>Core Web Vitals</h2><p>Google\'s metrics for measuring user experience.</p><h3>Key Metrics</h3><ul><li>Largest Contentful Paint (LCP)</li><li>First Input Delay (FID)</li><li>Cumulative Layout Shift (CLS)</li></ul><h2>Optimization Strategies</h2><p>Compress images, minify code, use CDN.</p><pre><code>// Lazy loading images
<img loading="lazy" src="image.jpg" alt="Lazy loaded image"></code></pre><p>Defer loading of non-critical resources.</p>',
            ],
            [
                'title' => 'Design Patterns in Software Development',
                'content' => '<h2>Creational Patterns</h2><p>Patterns for object creation.</p><h3>Common Patterns</h3><ul><li>Singleton</li><li>Factory Method</li><li>Abstract Factory</li><li>Builder</li><li>Prototype</li></ul><h2>Singleton Example</h2><pre><code>class Database {
  private static $instance = null;
  
  public static function getInstance() {
    if (self::$instance == null) {
      self::$instance = new Database();
    }
    return self::$instance;
  }
}</code></pre><p>Use proven solutions to common problems.</p>',
            ],
            [
                'title' => 'Authentication and Authorization in Modern Apps',
                'content' => '<h2>JWT Authentication</h2><p>JSON Web Tokens for stateless authentication.</p><h3>JWT Structure</h3><ul><li>Header</li><li>Payload</li><li>Signature</li></ul><h2>Laravel Sanctum</h2><p>Simple API authentication for SPAs and mobile apps.</p><pre><code>// Issue token
$token = $user->createToken(\'api-token\')->plainTextToken;</code></pre><p>Secure your APIs with proper authentication.</p>',
            ],
            [
                'title' => 'The Rise of Edge Computing',
                'content' => '<h2>What is Edge Computing?</h2><p>Process data closer to the source for faster response times.</p><h3>Benefits</h3><ul><li>Reduced latency</li><li>Bandwidth savings</li><li>Improved reliability</li><li>Enhanced security</li></ul><h2>Use Cases</h2><p>IoT devices, autonomous vehicles, real-time analytics.</p><pre><code>// Edge function example
export default {
  async fetch(request) {
    // Process at the edge
    return new Response(\'Hello from the edge!\');
  }
}</code></pre><p>Deploy compute resources closer to users.</p>',
            ],
            [
                'title' => 'Building Accessible Web Applications',
                'content' => '<h2>Web Accessibility (WCAG)</h2><p>Make web applications usable for everyone, including people with disabilities.</p><h3>Principles</h3><ul><li>Perceivable</li><li>Operable</li><li>Understandable</li><li>Robust</li></ul><h2>Accessibility Best Practices</h2><pre><code><!-- Semantic HTML -->
<nav>
  <ul>
    <li><a href="#main">Skip to main content</a></li>
  </ul>
</nav></code></pre><p>Use proper semantic HTML and ARIA attributes.</p>',
            ],
            [
                'title' => 'Quantum Computing: The Future of Computation',
                'content' => '<h2>Quantum vs Classical Computing</h2><p>Quantum computers leverage quantum mechanics for unprecedented computational power.</p><h3>Key Concepts</h3><ul><li>Qubits vs bits</li><li>Superposition</li><li>Entanglement</li><li>Quantum interference</li></ul><h2>Potential Applications</h2><p>Drug discovery, cryptography, optimization problems.</p><pre><code>// Simple quantum circuit (Qiskit)
from qiskit import QuantumCircuit

qc = QuantumCircuit(2, 2)
qc.h(0)
qc.cx(0, 1)
qc.measure_all()</code></pre><p>Quantum computing will revolutionize many fields.</p>',
            ],
            [
                'title' => 'Event-Driven Architecture with Message Queues',
                'content' => '<h2>Event-Driven Benefits</h2><p>Decouple services and improve scalability with asynchronous communication.</p><h3>Message Brokers</h3><ul><li>RabbitMQ</li><li>Apache Kafka</li><li>Redis</li><li>AWS SQS</li></ul><h2>Laravel Queues</h2><p>Laravel provides excellent queue support.</p><pre><code>// Dispatch job
ProcessVideo::dispatch($video);</code></pre><p>Handle time-consuming tasks asynchronously.</p>',
            ],
            [
                'title' => 'The Impact of AI on Software Testing',
                'content' => '<h2>AI in Testing</h2><p>Artificial intelligence is transforming how we approach software testing.</p><h3>AI-Powered Testing</h3><ul><li>Test case generation</li><li>Visual testing</li><li>Performance prediction</li><li>Defect detection</li></ul><h2>Tools and Techniques</h2><p>Machine learning models can predict bug-prone areas.</p><pre><code>// AI test generation example
const testCases = ai.generateTests(component);</code></pre><p>AI can generate comprehensive test suites automatically.</p>',
            ],
            [
                'title' => 'Functional Programming in JavaScript',
                'content' => '<h2>Functional Programming Principles</h2><p>Write more predictable and maintainable code with functional programming.</p><h3>Core Concepts</h3><ul><li>Pure functions</li><li>Immutability</li><li>Higher-order functions</li><li>Recursion</li></ul><h2>Functional Techniques</h2><pre><code>// Pure function
const add = (a, b) => a + b;

// Higher-order function
const filter = (predicate) => (array) => 
  array.filter(predicate);

// Immutability
const newArray = [...oldArray, newItem];</code></pre><p>Functional programming leads to more robust code.</p>',
            ],
            [
                'title' => 'Container Security Best Practices',
                'content' => '<h2>Securing Containerized Applications</h2><p>Containers introduce new security considerations.</p><h3>Security Principles</h3><ul><li>Minimal base images</li><li>Non-root user</li><li>Regular updates</li><li>Secrets management</li></ul><h2>Docker Security</h2><pre><code># Use specific image tags
FROM node:18-alpine

# Run as non-root user
USER node

# Avoid secrets in image
ENV API_KEY=${API_KEY}</code></pre><p>Follow the principle of least privilege.</p>',
            ],
            [
                'title' => 'The Evolution of CSS: From Floats to Grid',
                'content' => '<h2>CSS Layout Evolution</h2><p>CSS has come a long way from table-based layouts.</p><h3>Layout Methods</h3><ul><li>Floats and clears</li><li>Flexbox</li><li>CSS Grid</li><li>Container queries (upcoming)</li></ul><h2>CSS Grid Example</h2><pre><code>.container {
  display: grid;
  grid-template-columns: 1fr 2fr 1fr;
  gap: 1rem;
}

.item {
  grid-column: 2 / 3;
}</code></pre><p>CSS Grid provides powerful layout capabilities.</p>',
            ],
            [
                'title' => 'Monitoring and Observability in Production',
                'content' => '<h2>Observability Pillars</h2><p>Monitor your applications effectively in production.</p><h3>Three Pillars</h3><ul><li>Logs</li><li>Metrics</li><li>Traces</li></ul><h2>Monitoring Tools</h2><p>Use tools like Prometheus, Grafana, and ELK stack.</p><pre><code>// Application metrics
const responseTime = measureResponseTime(request);

// Log structured data
logger.info(\'User login\', {
  userId: user.id,
  timestamp: new Date(),
  ip: request.ip
});</code></pre><p>Comprehensive monitoring ensures system reliability.</p>',
            ],
            [
                'title' => 'Building Scalable Frontend Architectures',
                'content' => '<h2>Frontend Scaling Challenges</h2><p>As applications grow, frontend architecture becomes increasingly important.</p><h3>Scaling Strategies</h3><ul><li>Component libraries</li><li>State management</li><li>Code splitting</li><li>Performance optimization</li></ul><h2>React Best Practices</h2><pre><code>// Code splitting with React.lazy
const OtherComponent = React.lazy(() => 
  import(\'./OtherComponent\'));

function MyComponent() {
  return (
    <Suspense fallback={<div>Loading...</div>}>
      <OtherComponent />
    </Suspense>
  );
}</code></pre><p>Split code to improve loading performance.</p>',
            ],
            [
                'title' => 'The Future of Web Development',
                'content' => '<h2>Emerging Technologies</h2><p>The web development landscape is constantly evolving.</p><h3>Future Trends</h3><ul><li>WebAssembly</li><li>Edge computing</li><li>AI integration</li><li>Web Components</li><li>PWA enhancements</li></ul><h2>WebAssembly Example</h2><pre><code>// Rust code compiled to WebAssembly
#[wasm_bindgen]
pub fn fibonacci(n: u32) -> u32 {
    match n {
        0 => 0,
        1 => 1,
        _ => fibonacci(n - 1) + fibonacci(n - 2),
    }
}</code></pre><p>WebAssembly brings near-native performance to the web.</p>',
            ],
        ];

        foreach ($postsData as $postData) {
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
                    'thumbnail' => $thumbnails[array_rand($thumbnails)],
                    'status' => rand(0, 1) ? 'published' : 'draft',
                    'category_id' => $categories->random()->id,
                    'seo_title' => $postData['title'],
                    'seo_description' => Str::limit(strip_tags($postData['content']), 160),
                ]
            );

            if ($post->wasRecentlyCreated) {
                $randomTags = $tags->random(rand(2, 4));
                $tagIds = $randomTags->pluck('id')->toArray();
                $post->tags()->sync($tagIds);
            }
        }
    }
}
