# Git Branch Reference Guide

## Understanding Git Branches

### What is a Branch?

A Git branch is simply a **movable pointer** to a commit. It's like a bookmark that marks a specific point in your project's history.

```
Think of it like this:

Files/Commits:  📄📄📄📄📄📄📄📄  (These are your actual files and history)
                 ↑     ↑     ↑
Branches:       main  dev  feature  (These are just labels/pointers)
```

### Branch Visualization for This Repository

```
Commit History:
    
    35cce07 ←─── [main branch points here]
       |
       |  "Projet refactor complet" commit
       |  (contains all project files: index.php, models/, views/, etc.)
       |
       ↓
    17f3843 ←─── [copilot/check-main-repo-impact points here]
              ←─── [HEAD - you are here]
       
       "Initial plan" commit
```

Other branches exist independently:
- `Innovation` branch → points to commit 608474d
- `Offre` branch → points to commit 093a445
- `blog` branch → points to commit e492e0a
- `challenge` branch → points to commit f69ad14

### What Deleting a Branch Actually Does

**Deleting a branch removes the pointer, not the commits or files!**

#### Before Deleting `main`:
```
Commits:      A ← B ← C ← D ← E
                     ↑     ↑
Branches:          main  feature
```

#### After Deleting `main`:
```
Commits:      A ← B ← C ← D ← E
                           ↑
Branches:                feature
```

The commits A, B, C are still there! They're still accessible through `feature` branch.

### When Would Commits Be Lost?

Commits are only lost if:
1. No branch points to them (or their descendants)
2. Enough time passes for Git's garbage collection to run

#### Example of Orphaned Commits:

```
Before:
         main
           ↓
A ← B ← C ← D
         ↖
          E ← F
              ↑
           feature

After deleting main:
         
A ← B ← C ← D  (orphaned! will be garbage collected)
         ↖
          E ← F
              ↑
           feature
```

### Safe vs Unsafe Deletion

#### ✅ Safe to Delete (no data loss):
```bash
# When another branch contains all commits from the branch you want to delete
git branch -d safe-branch-name
```

#### ⚠️ Potentially Unsafe:
```bash
# When the branch has unique commits not in any other branch
git branch -D branch-with-unique-commits
```

### Commands to Check Before Deleting

```bash
# 1. See all branches and their last commits
git branch -v

# 2. See which branches contain all commits from 'main'
git branch --contains main

# 3. See commits that would become orphaned
git log --oneline main --not --remotes='*' --not --branches='*'

# 4. Compare two branches
git log --oneline main..feature  # commits in feature but not in main
git log --oneline feature..main  # commits in main but not in feature

# 5. Visualize all branches
git log --oneline --graph --all --decorate
```

### Best Practices

1. **Never delete the default branch** without careful planning
2. **Always check what commits would be orphaned** before deletion
3. **Use branch protection rules** on GitHub for important branches
4. **Create tags** for important milestones before reorganizing branches
5. **Communicate with team** before deleting shared branches

### Current Repository Structure

Based on the analysis:

- ✅ **Safe to delete `main`?** Technically yes, because `copilot/check-main-repo-impact` contains all commits from `main`
- ⚠️ **Should you delete `main`?** No, because:
  - It's the repository's default branch
  - Other people may have local copies based on `main`
  - It's the conventional primary branch
  - GitHub features expect it to exist

### If You Need to Change the Default Branch

Instead of deleting, do this:

1. **On GitHub:**
   - Go to Settings → Branches
   - Change the default branch to your preferred branch
   - Then optionally delete the old `main` after confirming the switch

2. **For collaborators:**
   ```bash
   git fetch origin
   git checkout new-default-branch
   git branch -d main  # delete their local main
   git branch --set-upstream-to=origin/new-default-branch
   ```

## Summary

- **Branches are pointers**, not containers of files
- **Deleting a branch** removes the pointer, not the data
- **Files in other branches** are completely unaffected
- **Commits remain safe** as long as they're referenced by another branch
- **Best practice**: Don't delete `main` unless you have a good reason and have set up an alternative default branch
