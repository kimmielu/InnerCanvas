-- ============================================================
-- InnerCanvas: 70 Complete Quests Insert Script (FIXED)
-- All single quotes properly escaped for MariaDB
-- ============================================================

-- CLEAR EXISTING QUESTS (optional, comment out if you want to keep existing)
-- DELETE FROM SideQuest;

-- ============================================================
-- MOOD 1: PANICKING / OVERWHELMED (10 quests)
-- ============================================================

INSERT INTO SideQuest (title, description, category, points, difficulty, is_active) VALUES

('5-4-3-2-1 Grounding Technique', 'Notice 5 things you see, 4 you can touch, 3 you hear, 2 you smell, 1 you taste. Ground yourself in sensory reality when panic overwhelms you.', 'Grounding', 10, 'Easy', TRUE),

('Cold Water Shock Reset', 'Splash your face or hands with cold water. Activates your body''s natural calming response instantly.', 'Somatic', 8, 'Easy', TRUE),

('Box Breathing (1-Minute Version)', 'Breathe in for 4 counts, hold for 4, out for 4, hold for 4. Repeat 5 times to regulate nervous system.', 'Breathing', 8, 'Easy', TRUE),

('Safe Space Setup', 'Create a physical safe spot with blankets, pillows, and comfort items. Your refuge when panic strikes.', 'Comfort', 15, 'Easy', TRUE),

('Tapping Release (EFT)', 'Use EFT tapping technique on collarbone, under eyes, under arms while breathing. Discharge anxiety from nervous system.', 'Somatic', 15, 'Medium', TRUE),

('Grounding Through Senses (Extended)', 'Spend 3 mins on each sense: sight, touch, sound, smell, taste. Full sensory engagement pulls you into present moment.', 'Grounding', 15, 'Easy', TRUE),

('Panic Journal Deep Dive', 'Write everything about the panic for 20 mins: triggers, body sensations, thoughts, what helped. Process and identify patterns.', 'Reflection', 20, 'Medium', TRUE),

('Trigger Mapping', 'Draw your triggers as a web with panic at center. Circle ones that resonate, write prevention actions for each.', 'Cognitive', 20, 'Medium', TRUE),

('Self-Compassion Letter', 'Write a letter to yourself from a loving perspective. Acknowledge panic, normalize it, celebrate survival, give permission to struggle.', 'Reflection', 20, 'Medium', TRUE),

('Controlled Panic Session (Advanced)', 'Intentionally trigger mild anxiety, observe it without fighting, realize it doesn''t destroy you. Build confidence in your survival.', 'Exposure', 25, 'Hard', TRUE),

-- ============================================================
-- MOOD 2: ANXIOUS / WORRIED (10 quests)
-- ============================================================

('Worry Container Ritual', 'Write worries on paper, put in sealed container. Contain anxiety physically and reclaim mental space.', 'Ritual', 12, 'Easy', TRUE),

('Worst-Case Scenario Journal', 'Write the worst outcome. Then write: how would you survive it? Realize you''re stronger than your anxiety.', 'Cognitive', 15, 'Medium', TRUE),

('Progressive Muscle Relaxation (Quick)', 'Tense and release each muscle group from toes to head. Teach your body that it''s safe.', 'Somatic', 12, 'Easy', TRUE),

('Anxiety Ladder (Exposure Therapy)', 'Create ladder of anxiety triggers (1-10). Do one level this week. Gradually face fears.', 'Exposure', 18, 'Medium', TRUE),

('Worry Time Scheduling', 'Schedule 15 mins daily for worry. Outside that time, defer anxious thoughts. Contain rumination.', 'Cognitive', 15, 'Easy', TRUE),

('Anxiety to Action Mapping', 'List anxieties. Ask: Can I control this? If yes, take action. If no, accept it. Transform worry into power.', 'Cognitive', 18, 'Medium', TRUE),

('Anxiety Body Scan (Somatic Awareness)', 'Scan body from head to toe, notice tension, consciously relax each area. Interrupt anxiety cycle.', 'Somatic', 15, 'Easy', TRUE),

('Gratitude as Anxiety Antidote', 'Write 10 things you''re grateful for with reasons why. Brain can''t be anxious and grateful simultaneously.', 'Mindfulness', 15, 'Easy', TRUE),

('Uncertainty Acceptance Practice', 'Sit with "I don''t know what will happen. And I''m okay with not knowing." Accept uncertainty as part of life.', 'Cognitive', 22, 'Hard', TRUE),

('Anxious Energy Release Dance', 'Play upbeat song, dance aggressively and freely for 12 mins. Move anxiety out of your nervous system.', 'Movement', 14, 'Easy', TRUE),

-- ============================================================
-- MOOD 3: DEPRESSED / LOW MOOD (10 quests)
-- ============================================================

('Micro Victories Log', 'Write 5 tiny things you did today (got out of bed, drank water, showered). See: you''re already winning.', 'Reflection', 8, 'Easy', TRUE),

('Sensory Comfort Box', 'Gather items that feel good: soft blanket, scented candle, photo, fidget toy, tea. Build your dopamine toolkit.', 'Comfort', 15, 'Easy', TRUE),

('Tiny Movement — One Song Dance', 'Pick one song. Move however feels okay. 4 mins of medicine.', 'Movement', 8, 'Easy', TRUE),

('Playlist Architecture', 'Curate 10-15 songs that match or lift your mood. Name it. Play it. Share it if brave.', 'Creative', 20, 'Medium', TRUE),

('Depression Conversation Letter', 'Write letter TO depression. Say what it''s stolen, how it lies, that you''re resisting it. Externalize depression.', 'Reflection', 18, 'Medium', TRUE),

('Behavioral Activation Micro-Scheduling', 'Schedule ONE activity that once brought joy. Do it even if you don''t feel like it. Action comes before motivation.', 'Behavioral', 16, 'Medium', TRUE),

('Sunshine + Movement Combination', 'Get outside in sunlight. Move gently for 20 mins. Two strongest natural antidepressants combined.', 'Movement', 16, 'Easy', TRUE),

('Connection Reaching Out', 'Text/call one person. Say: I''m struggling. I''m not okay. Break isolation. Ask for support.', 'Social', 16, 'Medium', TRUE),

('Creative Expression (Any Form)', 'Draw, write, collage, photograph, make music for 25 mins. Creation is resistance to depression.', 'Creative', 18, 'Easy', TRUE),

('Self-Compassion Meditation', 'Hand on heart. Repeat: I''m struggling. Others do too. I deserve kindness. This is temporary. I''m worthy.', 'Mindfulness', 16, 'Medium', TRUE),

-- ============================================================
-- MOOD 4: DISASSOCIATED / DETACHED (10 quests)
-- ============================================================

('Cold Water Shock', 'Cold water to face or hands. Activates body''s diving response. Snaps you back to NOW.', 'Somatic', 8, 'Easy', TRUE),

('Texture Exploration Quest', 'Feel 5 textures: silk, sandpaper, ice, velvet, rubber. Really feel each. Reconnect to your body.', 'Grounding', 10, 'Easy', TRUE),

('Taste Journey', 'Slowly taste 4 flavors: sweet, salty, sour, spicy. Let taste wake up your senses and bring you present.', 'Grounding', 10, 'Easy', TRUE),

('Grounding Object Ritual', 'Find an object you love. Keep with you. Hold it when dissociating. Let it anchor you to reality.', 'Grounding', 15, 'Easy', TRUE),

('5 Senses Grounding (Extended)', 'Spend 4 mins on each sense: sight, sound, smell, touch, taste. Full sensory engagement = full presence.', 'Grounding', 18, 'Medium', TRUE),

('Ice Cube Body Mapping', 'Drag ice cube slowly across your body. Feel temperature, feel aliveness. You''re here. You''re real.', 'Somatic', 15, 'Easy', TRUE),

('Breathwork With Body Awareness', 'Breathe slowly while mentally scanning your body. Link consciousness to physical body. Come back.', 'Somatic', 15, 'Medium', TRUE),

('Grounding Affirmations With Touch', 'Touch different body parts while saying affirmations. I''m here. I''m real. I''m alive. I''m safe.', 'Grounding', 14, 'Easy', TRUE),

('Movement Dance for Embodiment', 'Dance to strong beat music. Stomp, swing, move. Feel your body in space. You''re undeniably here.', 'Movement', 15, 'Easy', TRUE),

('Sensory Scavenger Hunt', 'Find items for each sense. Create grounding basket. Use it when dissociation hits.', 'Grounding', 18, 'Medium', TRUE),

-- ============================================================
-- MOOD 5: LACK OF CONCENTRATION / BRAIN FOG (10 quests)
-- ============================================================

('Movement Break Shuffle', 'Stand up. 1-2 mins of jumping, dancing, high knees. Reset your nervous system. Fog clears.', 'Movement', 8, 'Easy', TRUE),

('Pomodoro Power Session', '25 mins intense single-task focus + 5 min break. Prove to your brain that you CAN concentrate.', 'Productivity', 15, 'Medium', TRUE),

('Brain Dump Journal Purge', 'Write EVERYTHING in your head for 15 mins: tasks, worries, random thoughts. No filter. Declutter mental RAM.', 'Reflection', 12, 'Easy', TRUE),

('Focus Environment Optimization', 'Clean desk, remove distractions, adjust lighting, silence notifications. Engineer success.', 'Productivity', 18, 'Medium', TRUE),

('Single-Tasking Deep Dive', '45 mins on ONE task only. No switching. No multitasking. Prove multitasking was the problem.', 'Productivity', 22, 'Hard', TRUE),

('Caffeine + Timing Strategy', 'Drink caffeine 15 mins before focus session. Peak hits when you start. Ride the wave for 60 mins.', 'Productivity', 14, 'Easy', TRUE),

('Novel Stimulus Reset', 'When brain fog hits, do something new: different music, route, content. Novelty = dopamine = focus.', 'Productivity', 12, 'Easy', TRUE),

('Time Blindness Hack — Visual Timer', 'Get visual timer (app or sand). Make time visible. Your brain understands time better.', 'Productivity', 12, 'Easy', TRUE),

('Fidget While Focus', 'Use fidget tool during work session. Hands busy = brain focused. Work WITH your wiring, not against it.', 'Productivity', 15, 'Easy', TRUE),

('Accountability Buddy Check-In', 'Tell friend what you''re focusing on. They check in after 30 mins. External structure = brain takes it seriously.', 'Social', 14, 'Easy', TRUE),

-- ============================================================
-- MOOD 6: CREATIVE / INSPIRED (10 quests)
-- ============================================================

('Doodle Meditation Freestyle', 'Put on music. Doodle for 15 mins with no goal. Abstract, messy, beautiful. Let hands think.', 'Creative', 12, 'Easy', TRUE),

('Prompt-Based Creative Writing', 'Use writing prompt. Write continuously for 25 mins: fiction, journal, poem, whatever flows. No editing.', 'Creative', 16, 'Medium', TRUE),

('Inspiration Capture Collection', 'Screenshot/save inspiring images, quotes, ideas. Build inspiration folder. Fuel future creativity.', 'Creative', 10, 'Easy', TRUE),

('Mixed Media Art Creation', 'Gather magazines, paints, glue, scissors, paper. Create without plan. Mix media intuitively.', 'Creative', 20, 'Medium', TRUE),

('Collaboration Creation (With Someone)', 'Create with someone: write story together, make art, make playlist, take photos. Two minds = magic.', 'Creative', 18, 'Medium', TRUE),

('Idea Rapid-Fire Brainstorm', '20 mins: write as many ideas as possible. Quantity over quality. Wild is welcome. Find gems.', 'Creative', 15, 'Easy', TRUE),

('Medium Exploration (Try Something New)', 'Try creative medium you''ve never done: photography, clay, jewelry, music, poetry. Discover hidden passion.', 'Creative', 17, 'Medium', TRUE),

('Vulnerability Sharing (Post Your Work)', 'Take something you created. Share it. Post, send, show. Let world see your creativity.', 'Creative', 20, 'Hard', TRUE),

('Creative Constraint Challenge', 'Set constraint: 3 colors only, 5 sentences, nature items only, pen never lifts. Create brilliantly within limits.', 'Creative', 17, 'Medium', TRUE),

('Creative Vision Board Building', 'Gather images, words, create vision of creative future. What will you make? How will you share it?', 'Creative', 19, 'Medium', TRUE),

-- ============================================================
-- MOOD 7: HAPPY / ENERGIZED (10 quests)
-- ============================================================

('Dance Celebration Party', 'Play 3 favorite songs. DANCE. Celebrate yourself. Amplify joy. No choreography, pure celebration.', 'Movement', 12, 'Easy', TRUE),

('Gratitude Overflow Journal', 'Write 15-20 things you''re grateful for. Write freely, joyfully. Feel abundance overflow.', 'Reflection', 11, 'Easy', TRUE),

('Spread the Joy (One Act of Kindness)', 'Do one intentional kindness: compliment, message, help, smile. Watch happiness multiply.', 'Social', 13, 'Easy', TRUE),

('Kindness Chain Reaction', '30 mins of 5+ acts of kindness. Compliments, help, giving, calling. Watch ripple effect.', 'Social', 18, 'Medium', TRUE),

('Energy Celebration Playlist Creation', 'Create 15-20 song playlist of pure joy. Name it joyfully. Return to it whenever you need this energy.', 'Creative', 16, 'Easy', TRUE),

('Photo Capture Memory Making', 'Take 20-30 photos while happy. Create album, write captions. Prove happiness existed.', 'Reflection', 15, 'Easy', TRUE),

('Dream Mapping (What''s Next?)', 'What would you attempt if you knew you''d succeed? Write dreams, action steps. Take one step this week.', 'Reflection', 17, 'Medium', TRUE),

('Celebration of Self (Self-Love Ritual)', '30 mins celebrating YOU: candles, music, affirmations, pampering. I''m worthy. I''m capable. I''m enough.', 'Mindfulness', 18, 'Medium', TRUE),

('Connection Deepening (Quality Time)', '45 mins with someone you love: no phones, real conversation, laugh. Shared joy is amplified joy.', 'Social', 19, 'Medium', TRUE),

('Forward-Looking Vision Statement', 'Write vision from place of joy: I am becoming... I am worthy of... I will... Your future calling you.', 'Reflection', 17, 'Medium', TRUE);

-- ============================================================
-- VERIFICATION
-- ============================================================
-- Run this to verify all 70 were added:
-- SELECT COUNT(*) as total_quests FROM SideQuest;
-- SELECT category, COUNT(*) as count FROM SideQuest GROUP BY category;